<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    /**
     * Get user's cart items (enhanced to match web cart page)
     */
    public function index(Request $request)
    {
        $cartItems = CartItem::with(['product.category'])
                            ->where('user_id', $request->user()->id)
                            ->get();

        // Transform cart items to match web structure
        $transformedItems = $cartItems->map(function($item) {
            return [
                'id' => $item->cart_id,
                'quantity' => $item->quantity,
                'product' => [
                    'id' => $item->product->product_id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'price' => (float) $item->product->price,
                    'formatted_price' => 'Rs. ' . number_format($item->product->price, 2),
                    'image' => $item->product->image ? asset('storage/products/' . $item->product->image) : asset('images/placeholder.jpg'),
                    'stock' => $item->product->stock,
                    'category' => [
                        'id' => $item->product->category->category_id ?? null,
                        'name' => $item->product->category->name ?? 'Uncategorized',
                    ]
                ],
                'subtotal' => (float) ($item->quantity * $item->product->price),
                'formatted_subtotal' => 'Rs. ' . number_format($item->quantity * $item->product->price, 2)
            ];
        });

        $totalPrice = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        $totalItems = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $transformedItems,
                'summary' => [
                    'total_items' => $totalItems,
                    'total_price' => (float) $totalPrice,
                    'formatted_total' => 'Rs. ' . number_format($totalPrice, 2),
                    'is_empty' => $cartItems->isEmpty()
                ]
            ],
            'message' => 'Cart retrieved successfully'
        ]);
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);
        $user = $request->user();

        // Check if item already exists in cart
        $existingItem = CartItem::where('user_id', $user->id)
                               ->where('product_id', $request->product_id)
                               ->first();

        if ($existingItem) {
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
            $cartItem = $existingItem;
        } else {
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $cartItem->load('product'),
            'message' => 'Item added to cart successfully'
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,cart_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('cart_id', $request->cart_item_id)
                           ->where('user_id', $request->user()->id)
                           ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'data' => $cartItem->load('product'),
            'message' => 'Cart item updated successfully'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,cart_id'
        ]);

        $cartItem = CartItem::where('cart_id', $request->cart_item_id)
                           ->where('user_id', $request->user()->id)
                           ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully'
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clearCart(Request $request)
    {
        $deletedCount = CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Cart cleared. {$deletedCount} items removed."
        ]);
    }

    /**
     * Get cart item count
     */
    public function getCartCount(Request $request)
    {
        $count = CartItem::where('user_id', $request->user()->id)
                        ->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => ['count' => $count],
            'message' => 'Cart count retrieved successfully'
        ]);
    }
}