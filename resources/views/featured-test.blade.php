@extends('layouts.guest')

@section('title', 'Featured Products Preview')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center mb-4">Featured Products Styling Test</h1>
        <p class="text-center text-gray-600 mb-12">Testing clean card design without borders</p>
        
        {{-- Featured Products Section --}}
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Featured Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $featuredProducts = App\Models\Product::whereNotNull('image')->take(6)->get();
                @endphp
                
                @foreach($featuredProducts as $product)
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
            </div>
        </section>

        {{-- Comparison: Old vs New Style --}}
        <section class="bg-white p-8 rounded-lg">
            <h2 class="text-2xl font-bold mb-6">Styling Improvements</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Old Style Example --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-red-600">❌ Old Style (with borders/shadows)</h3>
                    <a href="#" class="bg-white text-black rounded-lg overflow-hidden shadow hover:shadow-lg transition-all duration-300 hover:scale-105 block">
                        <div class="w-full h-48 overflow-hidden border-b">
                            <img src="{{ asset('storage/products/1758611930_blazer.jpg') }}" 
                                 alt="Sample Product" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="p-4 text-center border">
                            <h4 class="text-lg font-semibold mb-1">Sample Product</h4>
                            <p class="text-sm text-gray-700 mb-2">With borders and shadows</p>
                            <p class="text-lg font-bold text-green-600">LKR 2,500.00</p>
                        </div>
                    </a>
                </div>

                {{-- New Style Example --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-green-600">✅ New Style (clean, no borders)</h3>
                    <a href="#" class="group block bg-white rounded-xl overflow-hidden transform transition-all duration-300 hover:scale-[1.02]">
                        <div class="relative w-full h-48 overflow-hidden rounded-t-xl">
                            <img src="{{ asset('storage/products/1758611930_blazer.jpg') }}" 
                                 alt="Sample Product" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300"></div>
                        </div>
                        <div class="p-5 bg-white rounded-b-xl">
                            <h4 class="text-lg font-bold text-gray-800 mb-2 line-clamp-1 group-hover:text-black transition-colors">Sample Product</h4>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">Clean design without extra borders</p>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-bold text-green-600">LKR 2,500.00</p>
                                <div class="text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                    View Details →
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <div class="text-center mt-8">
            <a href="{{ route('home') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection