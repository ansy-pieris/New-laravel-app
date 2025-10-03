<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });
        
        return view('cart.index', compact('cartItems', 'total'));
    }
    
    public function add(Request $request)
    {
        \Log::info('Cart add method called', ['request' => $request->all()]);
        
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check stock availability
        if ($product->stock < $request->quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available. Only ' . $product->stock . ' items left.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Not enough stock available.');
        }
        
        // Check if item already exists in cart
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();
        
        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $product->stock) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. Total would exceed available stock.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot add more items. Would exceed available stock.');
            }
            
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
            \Log::info('Cart item updated', ['cart_id' => $cartItem->cart_id, 'new_quantity' => $cartItem->quantity]);
        } else {
            $cartItem = CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
            \Log::info('Cart item created', ['cart_id' => $cartItem->cart_id, 'quantity' => $cartItem->quantity]);
        }
        
        // Get updated cart count
        $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity');
        
        // If it's a JSON request, return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'data' => [
                    'cart_count' => $cartCount,
                    'item' => $cartItem->load('product')
                ]
            ]);
        }
        
        // Otherwise redirect back with success message
        return redirect()->back()->with('success', 'Product added to cart!');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        // Check if item already exists in cart
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();
        
        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }
        
        return redirect()->back()->with('success', 'Product added to cart!');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        return redirect()->route('cart.index')->with('success', 'Cart updated!');
    }
    
    public function destroy($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();
        
        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
    }
}