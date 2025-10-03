<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index(Request $request)
    {
        $cartItems = CartItem::with('product')
                            ->where('user_id', $request->user()->id)
                            ->get();

        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total_items' => $cartItems->sum('quantity'),
                'total_price' => $total
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
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('id', $id)
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
    public function removeFromCart(Request $request, $id)
    {
        $cartItem = CartItem::where('id', $id)
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