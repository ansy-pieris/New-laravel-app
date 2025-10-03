@extends('layouts.app')

@section('title', 'Image Display Test')

@section('content')
<div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold text-center mb-8">Product Image Display Test</h1>
    
    {{-- Test 1: Direct Product Images --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4">Test 1: Direct Product Images from Storage</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $imageFiles = ['1758611930_blazer.jpg', '1758611952_shirt.jpg', '1758611969_jacket.jpg', '1758612010_shorts.jpg'];
            @endphp
            @foreach($imageFiles as $imageFile)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('storage/products/' . $imageFile) }}" 
                         alt="Test Image" 
                         class="w-full h-48 object-cover"
                         onerror="this.parentElement.innerHTML='<div class=\'w-full h-48 bg-red-100 flex items-center justify-center text-red-500\'>❌ Image Failed to Load</div>'">
                    <div class="p-2 text-sm text-center">{{ $imageFile }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Test 2: Products from Database --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4">Test 2: Products from Database</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $products = App\Models\Product::whereNotNull('image')->take(6)->get();
            @endphp
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-48 overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/products/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                 onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $product->image ?? 'No image file' }}</p>
                        <p class="text-green-600 font-bold">LKR {{ number_format($product->price, 2) }}</p>
                        @if(method_exists($product, 'getImageUrlAttribute'))
                            <p class="text-xs text-blue-600 mt-2">Using Image URL Attribute: {{ $product->image_url }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Test 3: Image File Existence Check --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4">Test 3: Image File Existence Check</h2>
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-3">Storage Directory Status:</h3>
            <ul class="space-y-2">
                <li>
                    <strong>Storage Symlink:</strong> 
                    @if(is_link(public_path('storage')))
                        <span class="text-green-600">✅ Exists and is a symlink</span>
                    @else
                        <span class="text-red-600">❌ Missing or not a symlink</span>
                    @endif
                </li>
                <li>
                    <strong>Products Directory:</strong>
                    @if(is_dir(public_path('storage/products')))
                        <span class="text-green-600">✅ Exists</span>
                        @php
                            $imageCount = count(glob(public_path('storage/products/*.{jpg,jpeg,png,gif}'), GLOB_BRACE));
                        @endphp
                        ({{ $imageCount }} image files found)
                    @else
                        <span class="text-red-600">❌ Directory not found</span>
                    @endif
                </li>
                <li>
                    <strong>Sample Image Test:</strong>
                    @if(file_exists(public_path('storage/products/1758611930_blazer.jpg')))
                        <span class="text-green-600">✅ Sample image exists</span>
                    @else
                        <span class="text-red-600">❌ Sample image missing</span>
                    @endif
                </li>
            </ul>
        </div>
    </div>

    {{-- Test 4: Manual Image Links --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4">Test 4: Manual Image URL Tests</h2>
        <div class="space-y-4">
            @php
                $testImages = [
                    'storage/products/1758611930_blazer.jpg',
                    'images/placeholder.jpg',
                    'images/Logo.png'
                ];
            @endphp
            @foreach($testImages as $imagePath)
                <div class="flex items-center space-x-4">
                    <img src="{{ asset($imagePath) }}" 
                         alt="Test" 
                         class="w-20 h-20 object-cover rounded border"
                         onerror="this.style.border='2px solid red'">
                    <div>
                        <p class="font-mono text-sm">{{ asset($imagePath) }}</p>
                        <p class="text-xs text-gray-500">
                            File exists: 
                            @if(file_exists(public_path($imagePath)))
                                <span class="text-green-600">✅ Yes</span>
                            @else
                                <span class="text-red-600">❌ No</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Back to Home
        </a>
    </div>
</div>
@endsection