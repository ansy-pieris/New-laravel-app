@extends('layouts.guest')
@section('title', $categoryName)

@php
  use Illuminate\Support\Str;

  // slug coming from controller: 'men', 'women', 'footwear', 'accessories'
  $slug = isset($category) ? $category : Str::slug($categoryName);

  // Map 4 hero sections (one per category). Replace image paths with yours.
  $heroes = [
    'men' => [
      'img'      => asset('images/heroes/men.jpg'),
      'title'    => "MEN'S WARDROBE",
      'subtitle' => 'Bold fits for every day.',
    ],
    'women' => [
      'img'      => asset('images/heroes/women.jpg'),
      'title'    => "WOMEN'S WARDROBE",
      'subtitle' => 'Statement pieces & everyday essentials.',
    ],
    'footwear' => [
      'img'      => asset('images/heroes/sneakers.jpeg'),
      'title'    => 'FOOTWEAR',
      'subtitle' => 'Step into comfort and style.',
    ],
    'accessories' => [
      'img'      => asset('images/heroes/watch.jpg'),
      'title'    => 'ACCESSORIES',
      'subtitle' => 'Finish your look with the right detail.',
    ],
  ];

  // Fallback if a new category appears
  $hero = $heroes[$slug] ?? [
    'img'      => asset('images/heroes/default.jpg'),
    'title'    => strtoupper($categoryName),
    'subtitle' => '',
  ];
@endphp

@section('content')
  <!-- Hero Section (auto-picks the right one for the current category) -->
  <div class="relative w-full h-[60vh] bg-black text-white mt-16">
    <img src="{{ $hero['img'] }}" alt="{{ $hero['title'] }}" class="w-full h-full object-contain opacity-30">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
      <h1 class="text-5xl font-bold tracking-wide">{{ $hero['title'] }}</h1>
      @if(!empty($hero['subtitle']))
        <p class="mt-3 text-white/80 max-w-2xl">{{ $hero['subtitle'] }}</p>
      @endif
    </div>
  </div>

  <!-- Product Grid -->
  <div class="bg-black text-white py-16 px-6 lg:px-20">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-12">

      @if ($products->count())
        @foreach ($products as $product)
          <div class="bg-neutral-900 group rounded-2xl overflow-hidden shadow-xl 
                      transition-all duration-700 ease-in-out hover:bg-white hover:scale-105">

            <div class="relative w-full h-80 overflow-hidden bg-white rounded-t-2xl">
              @if($product->image)
                <img src="{{ asset('storage/products/' . $product->image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover object-center transition-all duration-700 ease-in-out group-hover:scale-110">
              @else
                <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                  <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                  </svg>
                </div>
              @endif
            </div>

            <div class="p-6 space-y-3 transition-all duration-700 ease-in-out">
              <h2 class="text-2xl font-bold text-white group-hover:text-black transition-all duration-700">
                {{ $product->name }}
              </h2>
              <p class="text-lg text-gray-300 group-hover:text-black transition-all duration-700">
                Rs. {{ number_format($product->price, 2) }}
              </p>
              <a href="{{ route('products.show', $product) }}"
                 class="text-white group-hover:text-black underline text-base transition-all duration-700">
                View Details
              </a>
            </div>
          </div>
        @endforeach

        <div class="col-span-full mt-6">
          {{ $products->links() }}
        </div>
      @else
        <!-- Category-specific empty state -->
        <div class="col-span-full text-center">
          <p class="text-gray-300 text-lg">
            No products available in the <span class="font-semibold">{{ $categoryName }}</span> category right now.
          </p>
          <a href="{{ route('products.index') }}" class="inline-block mt-4 underline text-white/80 hover:text-white">
            Back to all categories
          </a>
        </div>
      @endif

    </div>
  </div>
@endsection
