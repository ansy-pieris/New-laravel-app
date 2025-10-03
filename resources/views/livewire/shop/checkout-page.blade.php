<div class="pt-24 pb-12 min-h-screen bg-black text-white">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8">Checkout</h1>

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-600 text-white rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="placeOrder" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Shipping Form -->
            <div class="space-y-4 bg-white bg-opacity-10 backdrop-blur-md p-6 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Shipping Details</h2>

                <div>
                    <label class="block mb-1 font-semibold">Recipient Name*</label>
                    <input type="text" wire:model="recipient_name" required 
                           class="w-full p-2 bg-gray-800 text-white rounded border @error('recipient_name') border-red-500 @else border-gray-600 @enderror" 
                           placeholder="John Doe">
                    @error('recipient_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-semibold">Phone Number*</label>
                    <input type="text" wire:model="phone" required 
                           class="w-full p-2 bg-gray-800 text-white rounded border @error('phone') border-red-500 @else border-gray-600 @enderror" 
                           placeholder="+94XXXXXXXXX">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-semibold">Street Address*</label>
                    <input type="text" wire:model="address" required 
                           class="w-full p-2 bg-gray-800 text-white rounded border @error('address') border-red-500 @else border-gray-600 @enderror" 
                           placeholder="123 Main Street">
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-semibold">City*</label>
                    <input type="text" wire:model="city" required 
                           class="w-full p-2 bg-gray-800 text-white rounded border @error('city') border-red-500 @else border-gray-600 @enderror" 
                           placeholder="Colombo">
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-semibold">Postal Code*</label>
                    <input type="text" wire:model="postal_code" required 
                           class="w-full p-2 bg-gray-800 text-white rounded border @error('postal_code') border-red-500 @else border-gray-600 @enderror" 
                           placeholder="00000">
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white bg-opacity-10 backdrop-blur-md p-6 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Your Order</h2>
                
                @if($items->count() > 0)
                    <table class="w-full text-left mb-6">
                        <thead>
                            <tr class="text-gray-300">
                                <th class="pb-2">Product</th>
                                <th class="pb-2">Quantity</th>
                                <th class="pb-2">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr class="border-b border-gray-700">
                                    <td class="py-2 flex items-center space-x-3">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ $item->product->image_url }}" 
                                                 class="w-12 h-12 object-cover rounded" 
                                                 alt="{{ $item->product->name }}">
                                        @else
                                            <div class="w-12 h-12 bg-gray-600 rounded flex items-center justify-center">
                                                <span class="text-xs text-gray-300">No Image</span>
                                            </div>
                                        @endif
                                        <span>{{ $item->product->name }}</span>
                                    </td>
                                    <td class="py-2">x{{ $item->quantity }}</td>
                                    <td class="py-2">Rs. {{ number_format($item->product->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="border-t border-gray-600 pt-4 text-sm space-y-1">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>Rs. {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        @if($tax > 0)
                            <div class="flex justify-between">
                                <span>Tax:</span>
                                <span>Rs. {{ number_format($tax, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-semibold">
                            <span>Total:</span>
                            <span>Rs. {{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-6 space-y-2">
                        <label class="block font-semibold mb-1">Payment Method</label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model.live="payment_method" value="cod" class="accent-white">
                            <span>Cash on Delivery</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model.live="payment_method" value="card" class="accent-white">
                            <span>Card Payment</span>
                        </label>

                        <!-- Card Details Section -->
                        @if($payment_method === 'card')
                            <div class="space-y-2 mt-4">
                                <label class="block font-semibold">Card Type</label>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" wire:model="card_type" value="visa" class="accent-white">
                                        <span>Visa</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" wire:model="card_type" value="mastercard" class="accent-white">
                                        <span>MasterCard</span>
                                    </label>
                                </div>
                                @error('card_type')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror

                                <div>
                                    <label class="block font-semibold">Card Number</label>
                                    <input type="text" wire:model="card_number" 
                                           class="w-full p-2 bg-gray-800 text-white rounded border @error('card_number') border-red-500 @else border-gray-600 @enderror" 
                                           placeholder="1234 5678 9012 3456">
                                    @error('card_number')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block font-semibold">Name on Card</label>
                                    <input type="text" wire:model="card_name" 
                                           class="w-full p-2 bg-gray-800 text-white rounded border @error('card_name') border-red-500 @else border-gray-600 @enderror" 
                                           placeholder="John Doe">
                                    @error('card_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block font-semibold">CVV</label>
                                    <input type="text" wire:model="card_cvv" 
                                           class="w-full p-2 bg-gray-800 text-white rounded border @error('card_cvv') border-red-500 @else border-gray-600 @enderror" 
                                           placeholder="123">
                                    @error('card_cvv')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <button type="submit" 
                            class="mt-6 w-full bg-red-600 text-white py-2 rounded text-lg font-semibold hover:bg-red-700 transition disabled:opacity-50"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Place Order</span>
                        <span wire:loading>Processing...</span>
                    </button>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-300 mb-4">Your cart is empty</p>
                        <a href="{{ route('shop') }}" class="inline-block bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                            Continue Shopping
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>