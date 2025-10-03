@extends('layouts.guest')
@section('title', $product->name)

@php use Illuminate\Support\Str; @endphp

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="min-h-screen bg-black text-white pt-24 px-6">
    <div class="max-w-6xl mx-auto">
        <!-- Product content will go here -->
        <script>
            function updateCartCounter() {
                const cartCount = window.cartData ? window.cartData.cart_count || 0 : 0;
                
                console.log('Updating cart counter with count:', cartCount);
                console.log('Window cartData:', window.cartData);
                
                // Dispatch the event with cart count data for Alpine.js
                const event = new CustomEvent('cart-updated', {
                    detail: { count: cartCount }
                });
                window.dispatchEvent(event);
                console.log('Dispatched cart-updated event:', event);
                
                // Also dispatch Livewire event for server-side refresh
                if (window.Livewire) {
                    console.log('Dispatching Livewire cartUpdated event');
                    window.Livewire.dispatch('cartUpdated');
                } else {
                    console.log('Livewire not available');
                }
            }{{-- Success / Error notifications --}}
  @if (session('success'))
    <div id="successToast" class="fixed top-24 right-6 z-50 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300">
      <div class="flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('successToast');
        if (toast) {
          setTimeout(() => { toast.classList.remove('translate-x-full'); }, 100);
          setTimeout(() => { 
            toast.classList.add('translate-x-full'); 
            setTimeout(() => toast.remove(), 300);
          }, 3000);
        }
      });
    </script>
  @endif
  @if (session('error'))
    <div id="errorToast" class="fixed top-24 right-6 z-50 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300">
      <div class="flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span>{{ session('error') }}</span>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('errorToast');
        if (toast) {
          setTimeout(() => { toast.classList.remove('translate-x-full'); }, 100);
          setTimeout(() => { 
            toast.classList.add('translate-x-full'); 
            setTimeout(() => toast.remove(), 300);
          }, 3000);
        }
      });
    </script>
  @endif

  <div class="min-h-screen bg-black text-white pt-24 px-6 lg:px-20 pb-16">
    <div class="max-w-6xl mx-auto flex flex-col lg:flex-row items-center gap-10">
      {{-- Product Image --}}
      <div class="w-full lg:w-1/2">
        <div class="w-full h-96 lg:h-[500px] overflow-hidden bg-white rounded-xl shadow-lg">
          @if($product->image)
            <img src="{{ asset('storage/products/' . $product->image) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover">
          @else
            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
              No Image Available
            </div>
          @endif
        </div>
      </div>

      {{-- Product Details + Add to Cart --}}
      <div class="w-full lg:w-1/2 space-y-6">
        <h1 class="text-4xl font-bold">{{ $product->name }}</h1>
        <p class="text-2xl font-semibold text-blue-300">Rs. {{ number_format($product->price, 2) }}</p>

        <p class="text-gray-300 whitespace-pre-line">{{ $product->description }}</p>

        {{-- Stock info --}}
        @if ($product->stock <= 0)
          <p class="text-sm text-red-500 font-semibold">Out of stock</p>
        @elseif ($product->stock < 10)
          <p class="text-sm text-yellow-400">Only {{ $product->stock }} left in stock</p>
        @else
          <p class="text-sm text-green-400">In Stock</p>
        @endif

        <p class="text-sm text-gray-400">
          Category: {{ $product->category->name ?? 'Uncategorized' }}
        </p>

        {{-- Add to Cart (auth required) --}}
        @if ($product->stock > 0)
          @auth
            <div class="space-y-4 mt-4">
              <form id="addToCartForm" data-product-id="{{ $product->product_id }}">
                @csrf
                <div>
                  <label for="quantity" class="block mb-2 font-semibold">Quantity</label>
                  <input type="number" id="quantity" name="quantity" min="1" max="{{ $product->stock }}" value="1"
                         class="w-24 bg-black border border-white text-white px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-white">
                </div>
                <button type="submit" id="addToCartBtn" class="bg-white text-black font-bold px-6 py-3 rounded hover:bg-gray-300 transition disabled:opacity-50 disabled:cursor-not-allowed">
                  <span class="btn-text">Add to Cart</span>
                  <span class="btn-loading hidden">Adding...</span>
                </button>
              </form>
            </div>
            
            <script>
            document.getElementById('addToCartForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const btn = document.getElementById('addToCartBtn');
                const btnText = btn.querySelector('.btn-text');
                const btnLoading = btn.querySelector('.btn-loading');
                const form = e.target;
                const productId = form.dataset.productId;
                const quantity = document.getElementById('quantity').value;
                
                // Show loading state
                btn.disabled = true;
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
                
                try {
                    const response = await fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: parseInt(quantity)
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast('✅ Product added to cart successfully!', 'success');
                        
                        // Store cart data globally for other scripts to use
                        window.cartData = data.data || { cart_count: 0 };
                        
                        console.log('Cart data received:', window.cartData);
                        
                        updateCartCounter();
                    } else {
                        showToast('❌ ' + (data.message || 'Failed to add product to cart'), 'error');
                    }
                } catch (error) {
                    console.error('Cart error:', error);
                    showToast('❌ Something went wrong. Please try again.', 'error');
                } finally {
                    // Reset button state
                    btn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                }
            });
            
            function showToast(message, type = 'success') {
                const toastId = 'toast-' + Date.now();
                const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
                const icon = type === 'success' ? 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                
                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `fixed top-24 right-6 z-50 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300`;
                toast.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${icon}
                        </svg>
                        <span class="font-medium">${message}</span>
                    </div>
                `;
                
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => { toast.classList.remove('translate-x-full'); }, 100);
                
                // Animate out after 3 seconds
                setTimeout(() => { 
                    toast.classList.add('translate-x-full'); 
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
            
            function updateCartCounter() {
                // Get the cart count from the response data
                const cartCount = window.cartData ? window.cartData.cart_count || 0 : 0;
                
                // Dispatch the event with cart count data for Alpine.js
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { count: cartCount }
                }));
                
                // Also dispatch Livewire event for server-side refresh
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated');
                }
                
                console.log('Cart counter updated with count:', cartCount);
            }
            </script>
          @else
            <a href="{{ route('login') }}"
               class="inline-block mt-4 bg-white/0 border border-white/30 text-white px-6 py-3 rounded hover:bg-white/5 transition">
              Login to buy
            </a>
          @endauth
        @else
          <div class="mt-6">
            <button disabled
                    class="bg-gray-600 text-white font-bold px-6 py-3 rounded cursor-not-allowed opacity-50">
              Out of Stock
            </button>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
