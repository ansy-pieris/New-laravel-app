@extends('layouts.guest')
@section('title', 'Home')

@section('content')
<div class="pt-20">

  {{-- Carousel Section --}}
  <section class="relative w-full h-[500px] overflow-hidden">
    <div id="carousel" class="relative w-full h-full" data-carousel="slide">
      <div class="relative h-full overflow-hidden rounded-lg">
        {{-- Item 1 --}}
        <div class="hidden duration-[1200ms] ease-in-out" data-carousel-item="active">
          <img src="{{ asset('images/Ares3.jpg') }}" 
               class="absolute block w-full h-full object-contain" 
               alt="Slide 1"
               onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';">
        </div>
        {{-- Item 2 --}}
        <div class="hidden duration-[1200ms] ease-in-out" data-carousel-item>
          <img src="{{ asset('images/hero3.webp') }}" 
               class="absolute block w-full h-full object-cover" 
               alt="Slide 2"
               onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';">
        </div>
        {{-- Item 3 --}}
        <div class="hidden duration-[1200ms] ease-in-out" data-carousel-item>
          <img src="{{ asset('images/hero2.jpg') }}" 
               class="absolute block w-full h-full object-cover" 
               alt="Slide 3"
               onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';">
        </div>
      </div>

      {{-- Slider indicators --}}
      <div class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2">
        <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
        <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white" aria-label="Slide 2" data-carousel-slide-to="1"></button>
        <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white" aria-label="Slide 3" data-carousel-slide-to="2"></button>
      </div>

      {{-- Slider controls --}}
      <button type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 group-hover:bg-black/50">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </span>
      </button>
      <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 group-hover:bg-black/50">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </span>
      </button>
    </div>
  </section>

  {{-- Welcome Message --}}
  <div class="container mx-auto py-10 text-center">
    <h2 class="text-4xl font-bold mb-4">Welcome to ARES</h2>
    <p class="text-lg max-w-2xl mx-auto">Where power meets fashion. Discover bold apparel, empowering accessories, and footwear designed to make you stand out.</p>
  </div>

  {{-- Category Cards --}}
  <section class="container mx-auto px-4 py-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
    @php
      $categories = [
        ['title' => 'Men',        'img' => asset('images/men.jpg'), 'slug' => 'men'],
        ['title' => 'Women',      'img' => asset('images/women.jpg'), 'slug' => 'women'],
        ['title' => 'Footwear',   'img' => asset('images/sneakers.jpeg'), 'slug' => 'footwear'],
        ['title' => 'Accessories','img' => asset('images/watch.jpg'), 'slug' => 'accessories'],
      ];
    @endphp

    @foreach ($categories as $cat)
      <a href="{{ route('products.category', ['category' => $cat['slug']]) }}" class="relative group overflow-hidden rounded-lg shadow-lg h-64">
        <img src="{{ $cat['img'] }}" alt="{{ $cat['title'] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        <div class="absolute inset-0 bg-black bg-opacity-20 flex flex-col justify-center items-center text-center">
          <h3 class="text-xl font-semibold text-white mb-2">{{ $cat['title'] }}</h3>
          <span class="bg-white text-black px-4 py-2 rounded hover:bg-gray-200 transition">Shop Now</span>
        </div>
      </a>
    @endforeach
  </section>

  {{-- Featured Products --}}
  <section class="container mx-auto px-4 py-10">
    <h2 class="text-3xl font-bold text-center mb-8">Featured Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      @if($featuredProducts->count() > 0)
        @foreach($featuredProducts->take(6) as $product)
          <a href="{{ route('products.show', $product) }}" class="group block bg-white rounded-xl overflow-hidden transform transition-all duration-300 hover:scale-[1.02]">
            {{-- Image Container - No borders, perfect fit --}}
            <div class="relative w-full h-64 overflow-hidden rounded-t-xl">
              @if($product->image)
                <img src="{{ asset('storage/products/' . $product->image) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                  <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                  </svg>
                </div>
              @endif
              {{-- Optional: Subtle overlay for better text readability --}}
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300"></div>
            </div>
            
            {{-- Product Info - Clean styling --}}
            <div class="p-5 bg-white rounded-b-xl">
              <h4 class="text-lg font-bold text-gray-800 mb-2 line-clamp-1 group-hover:text-black transition-colors">{{ $product->name }}</h4>
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
              <div class="flex items-center justify-between">
                <p class="text-xl font-bold text-green-600">LKR {{ number_format($product->price, 2) }}</p>
                <div class="text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">
                  View Details →
                </div>
              </div>
            </div>
          </a>
        @endforeach
      @else
        {{-- Fallback to static images if no featured products --}}
        @for ($i = 1; $i <= 6; $i++)
          <a href="{{ route('products.index') }}" class="group block bg-white rounded-xl overflow-hidden transform transition-all duration-300 hover:scale-[1.02]">
            <div class="relative w-full h-64 overflow-hidden rounded-t-xl">
              <img src="{{ asset('images/featured' . $i . '.jpg') }}" 
                   alt="Featured Product {{ $i }}" 
                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-5 bg-white rounded-b-xl">
              <h4 class="text-lg font-bold text-gray-800 mb-2">Featured Item {{ $i }}</h4>
              <p class="text-sm text-gray-600">Explore our product catalog</p>
            </div>
          </a>
        @endfor
      @endif
    </div>
  </section>

  {{-- Store Features --}}
  <section class="container mx-auto px-4 py-16 bg-gradient-to-br from-gray-900 to-black">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold text-white mb-4">Why Choose Our Store?</h2>
      <p class="text-gray-300 max-w-2xl mx-auto">Experience premium apparel shopping with our commitment to quality, speed, and customer satisfaction.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      {{-- Fast Delivery --}}
      <div class="bg-gray-800 rounded-xl p-8 shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 transform hover:-translate-y-2 border border-gray-700 hover:border-blue-500/50">
        <div class="text-center">
          <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-6 ring-2 ring-blue-500/30">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-4">Fast Delivery</h3>
          <p class="text-gray-300 leading-relaxed">Get your favorite apparel delivered to your doorstep within 2-3 business days. Free shipping on orders over Rs. 5,000.</p>
        </div>
      </div>

      {{-- Quality Products --}}
      <div class="bg-gray-800 rounded-xl p-8 shadow-2xl hover:shadow-green-500/20 transition-all duration-300 transform hover:-translate-y-2 border border-gray-700 hover:border-green-500/50">
        <div class="text-center">
          <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6 ring-2 ring-green-500/30">
            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-4">Premium Quality</h3>
          <p class="text-gray-300 leading-relaxed">Carefully curated collection of high-quality fabrics and materials. Each piece is selected for comfort, durability, and style.</p>
        </div>
      </div>

      {{-- Quality Guarantee --}}
      <div class="bg-gray-800 rounded-xl p-8 shadow-2xl hover:shadow-purple-500/20 transition-all duration-300 transform hover:-translate-y-2 border border-gray-700 hover:border-purple-500/50">
        <div class="text-center">
          <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-6 ring-2 ring-purple-500/30">
            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-4">Quality Guarantee</h3>
          <p class="text-gray-300 leading-relaxed">30-day return policy with full money-back guarantee. If you're not completely satisfied, we'll make it right.</p>
        </div>
      </div>
    </div>

    {{-- Additional Features Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
      {{-- Secure Payment --}}
      <div class="bg-gray-800 rounded-xl p-6 shadow-2xl hover:shadow-yellow-500/20 transition-all duration-300 flex items-center border border-gray-700 hover:border-yellow-500/50">
        <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center mr-6 ring-2 ring-yellow-500/30">
          <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
        <div>
          <h4 class="text-lg font-semibold text-white mb-2">Secure Payment</h4>
          <p class="text-gray-300">Safe and secure online payments with multiple payment options including cash on delivery.</p>
        </div>
      </div>

      {{-- 24/7 Support --}}
      <div class="bg-gray-800 rounded-xl p-6 shadow-2xl hover:shadow-red-500/20 transition-all duration-300 flex items-center border border-gray-700 hover:border-red-500/50">
        <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mr-6 ring-2 ring-red-500/30">
          <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div>
          <h4 class="text-lg font-semibold text-white mb-2">24/7 Support</h4>
          <p class="text-gray-300">Our dedicated customer support team is here to help you with any questions or concerns.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- Flowbite Carousel JavaScript --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</div>
@endsection
