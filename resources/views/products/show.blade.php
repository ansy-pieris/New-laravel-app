@extends('layouts.guest')
@section('title', $product->name)

@php use Illuminate\Support\Str; @endphp

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
  {{-- Success / Error notifications with toast --}}
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
            <div class="mt-6">
              <form id="addToCartForm" action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                
                {{-- Quantity and Add to Cart in a row --}}
                <div class="flex items-end gap-4">
                  <div class="flex-shrink-0">
                    <label for="quantity" class="block mb-2 text-sm font-semibold text-gray-300">Quantity</label>
                    <input type="number" name="quantity" min="1" max="{{ $product->stock }}" value="1"
                           class="w-20 bg-gray-900 border border-gray-600 text-white px-3 py-2 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all">
                  </div>
                  <div class="flex-1">
                    <button type="submit" class="w-full bg-white text-black font-bold px-6 py-3 rounded-lg hover:bg-gray-200 transition-all duration-200 transform hover:scale-105 active:scale-95">
                      <i class="fas fa-shopping-cart mr-2"></i>
                      Add to Cart
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('addToCartForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = new FormData(form);
                        const submitButton = form.querySelector('button[type="submit"]');
                        const originalText = submitButton.innerHTML;
                        
                        // Disable button and show loading
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
                        
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Store cart data globally for the counter
                                window.cartData = data.data;
                                
                                // Show success toast
                                showToast('🛒 ' + data.message, 'success');
                                
                                // Update cart counter
                                updateCartCounter();
                            } else {
                                showToast('❌ ' + (data.message || 'Failed to add to cart'), 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('❌ An error occurred. Please try again.', 'error');
                        })
                        .finally(() => {
                            // Re-enable button
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        });
                    });
                }
            });
            
            function updateCartCounter() {
                const cartCount = window.cartData ? window.cartData.cart_count || 0 : 0;
                
                // Dispatch the event with cart count data for Alpine.js
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { count: cartCount }
                }));
                
                // Also dispatch Livewire event for server-side refresh
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated');
                }
            }
            
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
