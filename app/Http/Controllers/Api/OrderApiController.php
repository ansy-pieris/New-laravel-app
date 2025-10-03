<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\ShippingAddress;
use App\Services\GoogleMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    /**
     * Checkout and create order
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.full_name' => 'required|string|max:255',
            'shipping_address.address_line_1' => 'required|string|max:255',
            'shipping_address.address_line_2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'payment_method' => 'required|string|in:credit_card,paypal,cash_on_delivery'
        ]);

        $user = $request->user();
        
        // Get cart items
        $cartItems = CartItem::with('product')
                            ->where('user_id', $user->id)
                            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Calculate total
            $total = $cartItems->sum(function($item) {
                return $item->quantity * $item->product->price;
            });

            // Create shipping address
            $shippingAddress = ShippingAddress::create([
                'user_id' => $user->id,
                'full_name' => $request->shipping_address['full_name'],
                'address_line_1' => $request->shipping_address['address_line_1'],
                'address_line_2' => $request->shipping_address['address_line_2'],
                'city' => $request->shipping_address['city'],
                'state' => $request->shipping_address['state'],
                'postal_code' => $request->shipping_address['postal_code'],
                'country' => $request->shipping_address['country']
            ]);

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'shipping_address_id' => $shippingAddress->id
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'total' => $cartItem->quantity * $cartItem->product->price
                ]);
            }

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            // Send order confirmation email via Google API
            try {
                $googleMailService = new GoogleMailService();
                $googleMailService->sendOrderConfirmation($order->load(['orderItems.product', 'shippingAddress']), $user->email);
            } catch (\Exception $e) {
                // Log email error but don't fail the order
                \Log::error('Order confirmation email failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => $order->load(['orderItems.product', 'shippingAddress']),
                'message' => 'Order placed successfully! Confirmation email sent.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's orders
     */
    public function getUserOrders(Request $request)
    {
        $orders = Order::with(['orderItems.product'])
                      ->where('user_id', $request->user()->id)
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Orders retrieved successfully'
        ]);
    }

    /**
     * Get specific order details
     */
    public function getOrderDetails(Request $request, $id)
    {
        $order = Order::with(['orderItems.product', 'shippingAddress'])
                     ->where('id', $id)
                     ->where('user_id', $request->user()->id)
                     ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order details retrieved successfully'
        ]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::where('id', $id)
                     ->where('user_id', $request->user()->id)
                     ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order cancelled successfully'
        ]);
    }

    /**
     * Track order status
     */
    public function trackOrder(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
                     ->where('user_id', $request->user()->id)
                     ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at,
                'estimated_delivery' => $order->created_at->addDays(7)
            ],
            'message' => 'Order tracking information retrieved successfully'
        ]);
    }
}