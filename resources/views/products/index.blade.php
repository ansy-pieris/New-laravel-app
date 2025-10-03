@extends('layouts.guest')
@section('title', 'Store')

@php
  // Optional hero for the store landing
  $storeHero = asset('images/heroes/store.jpg'); // put a banner at public/images/heroes/store.jpg
@endphp

@section('content')
  {{-- Hero --}}
  <div class="relative w-full h-[40vh] bg-black text-white mt-16">
    <img src="{{ $storeHero }}" alt="Store" class="w-full h-full object-cover opacity-30">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
      <h1 class="text-4xl md:text-5xl font-bold tracking-wide">SHOP THE COLLECTIONS</h1>
      <p class="mt-3 text-white/80 max-w-2xl">Explore Men, Women, Footwear, and Accessories</p>
    </div>
  </div>

  {{-- Category Grid --}}
  <div class="bg-black text-white py-16 px-6 lg:px-20">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-2xl md:text-3xl font-bold mb-8">Browse by Category</h2>

      @if(collect($categories)->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          @foreach ($categories as $cat)
            @php
              // Support both array and Eloquent object shapes
              $name  = is_array($cat) ? $cat['name']  : $cat->name;
              $slug  = is_array($cat) ? $cat['slug']  : $cat->slug;
              $image = is_array($cat) ? $cat['image']
                                      : ($cat->image ? asset('storage/'.$cat->image) : asset('images/categories/'.$slug.'.jpg'));
            @endphp

            <a href="{{ route('products.category', ['category' => $slug]) }}"
               class="group relative rounded-2xl overflow-hidden border border-white/10 hover:border-white/20 hover:scale-[1.02] transition duration-500">
              <div class="w-full h-48 overflow-hidden bg-white">
                <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
              </div>
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
              <div class="absolute bottom-0 p-4">
                <div class="text-lg font-semibold">{{ strtoupper($name) }}</div>
                <span class="inline-block mt-1 text-sm underline">Shop {{ $name }}</span>
              </div>
            </a>
          @endforeach
        </div>
      @else
        <div class="text-center text-white/70 py-12">
          No categories available right now.
          <div class="mt-3">
            <a href="{{ route('home') }}" class="underline hover:text-white">Back to Home</a>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection
