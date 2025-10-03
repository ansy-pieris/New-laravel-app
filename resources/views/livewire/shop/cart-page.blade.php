<div>
  {{-- Flash Messages --}}
  @if (session()->has('success'))
    <div class="mb-4 px-4 py-3 rounded bg-green-600/10 text-green-300 border border-green-600/20">
      {{ session('success') }}
    </div>
  @endif
  
  @if (session()->has('error'))
    <div class="mb-4 px-4 py-3 rounded bg-red-600/10 text-red-300 border border-red-600/20">
      {{ session('error') }}
    </div>
  @endif

  @guest
    <div class="text-center py-12">
      <svg class="w-24 h-24 mx-auto text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m15 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
      </svg>
      <h3 class="text-xl font-semibold text-gray-400 mb-4">Please login to view your cart</h3>
      <a href="{{ route('login') }}" class="inline-block bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
        Login to Continue
      </a>
    </div>
  @else
    @if($items->isEmpty())
      <div class="text-center py-12">
        <svg class="w-24 h-24 mx-auto text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m15 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-400 mb-4">Your cart is empty</h3>
        <p class="text-gray-500 mb-6">Add some amazing products to get started!</p>
        <a href="{{ route('home') }}" class="inline-block bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
          Start Shopping
        </a>
      </div>
    @else
      <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg overflow-hidden">
        {{-- Cart Items Table --}}
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-black bg-opacity-30">
              <tr class="border-b border-gray-600">
                <th class="py-4 px-6 font-semibold">Image</th>
                <th class="py-4 px-6 font-semibold">Product</th>
                <th class="py-4 px-6 font-semibold">Price</th>
                <th class="py-4 px-6 font-semibold">Quantity</th>
                <th class="py-4 px-6 font-semibold">Total</th>
                <th class="py-4 px-6 font-semibold">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($items as $item)
                <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5 transition
                  {{ $item->product && $item->product->stock < $item->quantity ? 'bg-red-900 bg-opacity-20' : '' }}">
                  <td class="py-4 px-6">
                    @if($item->product && $item->product->image)
                      <img src="{{ $item->product->image_url }}" 
                           alt="{{ $item->product->name }}" 
                           class="w-18 h-20 object-cover rounded-lg">
                    @else
                      <div class="w-18 h-20 bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                      </div>
                    @endif
                  </td>
                  <td class="py-4 px-6">
                    <div class="font-semibold text-white">
                      {{ $item->product->name ?? 'Product Unavailable' }}
                    </div>
                    @if($item->product && $item->product->stock < $item->quantity)
                      <p class="text-sm text-yellow-400 mt-1">
                        ⚠️ Only {{ $item->product->stock }} available in stock
                      </p>
                    @elseif(!$item->product)
                      <p class="text-sm text-red-400 mt-1">
                        ❌ Product no longer available
                      </p>
                    @endif
                  </td>
                  <td class="py-4 px-6 text-white font-semibold">
                    Rs. {{ number_format($item->product->price ?? 0, 2) }}
                  </td>
                  <td class="py-4 px-6">
                    <div class="flex items-center space-x-2" wire:key="quantity-controls-{{ $item->cart_id }}">
                      <button wire:click="decrementQuantity({{ $item->cart_id }})"
                              wire:loading.attr="disabled"
                              wire:target="decrementQuantity, updateQuantity"
                              class="w-8 h-8 bg-gray-700 hover:bg-gray-600 text-white rounded-full flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                              @if($item->quantity <= 1) disabled @endif>
                        <div wire:loading.remove wire:target="decrementQuantity, updateQuantity">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                          </svg>
                        </div>
                        <div wire:loading wire:target="decrementQuantity, updateQuantity">
                          <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                        </div>
                      </button>
                      <span class="w-12 text-center font-semibold text-white" wire:loading.class="opacity-50" wire:target="incrementQuantity, decrementQuantity, updateQuantity">{{ $item->quantity }}</span>
                      <button wire:click="incrementQuantity({{ $item->cart_id }})"
                              wire:loading.attr="disabled"
                              wire:target="incrementQuantity, updateQuantity"
                              class="w-8 h-8 bg-gray-700 hover:bg-gray-600 text-white rounded-full flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                              @if($item->product && $item->quantity >= $item->product->stock) disabled @endif>
                        <div wire:loading.remove wire:target="incrementQuantity, updateQuantity">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                          </svg>
                        </div>
                        <div wire:loading wire:target="incrementQuantity, updateQuantity">
                          <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                        </div>
                      </button>
                    </div>
                  </td>
                  <td class="py-4 px-6 text-white font-semibold">
                    Rs. {{ number_format(($item->product->price ?? 0) * $item->quantity, 2) }}
                  </td>
                  <td class="py-4 px-6">
                    <button wire:click="removeItem({{ $item->cart_id }})"
                            onclick="return confirm('Remove this item from cart?')"
                            class="text-red-400 hover:text-red-300 font-semibold transition">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Cart Summary --}}
        <div class="bg-black bg-opacity-40 px-6 py-6">
          <div class="flex justify-between items-center mb-4">
            <button wire:click="clearCart"
                    onclick="return confirm('Clear entire cart?')"
                    class="text-red-400 hover:text-red-300 font-semibold transition flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              Clear Cart
            </button>
            <div class="text-right">
              <div class="text-gray-300 mb-2">
                <span>Subtotal: Rs. {{ number_format($subtotal, 2) }}</span>
              </div>
              <div class="text-gray-300 mb-2">
                <span>Tax (8%): Rs. {{ number_format($tax, 2) }}</span>
              </div>
              <div class="text-2xl font-bold text-white border-t border-gray-600 pt-2">
                <span>Total: Rs. {{ number_format($total, 2) }}</span>
              </div>
            </div>
          </div>

          <div class="flex justify-between items-center">
            <a href="{{ route('home') }}" 
               class="text-blue-400 hover:text-blue-300 font-semibold transition flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              Continue Shopping
            </a>
            <a href="{{ route('checkout') }}"
               class="bg-white text-black px-8 py-3 rounded-lg font-semibold hover:bg-gray-200 transition flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m15 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Proceed to Checkout
            </a>
          </div>
        </div>
      </div>
    @endif
  @endguest
</div>
