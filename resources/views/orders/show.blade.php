@extends('layouts.app')
@section('title', 'Order Details')

@section('content')
<div class="min-h-screen bg-black text-white pt-20">
    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('orders.track') }}" 
               class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors">
                ← Back to Orders
            </a>
        </div>

        {{-- Order Header --}}
        <div class="bg-gray-900 rounded-lg border border-gray-700 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Order #{{ $order->order_id }}</h1>
                    <p class="text-gray-400">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="px-4 py-2 rounded-full text-sm font-medium
                        {{ $order->status === 'delivered' ? 'bg-green-600 text-green-100' : '' }}
                        {{ $order->status === 'shipped' ? 'bg-blue-600 text-blue-100' : '' }}
                        {{ $order->status === 'pending' ? 'bg-yellow-600 text-yellow-100' : '' }}
                        {{ $order->status === 'paid' ? 'bg-purple-600 text-purple-100' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-600 text-red-100' : '' }}">
                        {{ $order->getStatusLabelAttribute() }}
                    </span>
                </div>
            </div>

            @if($canManage && $order->canBeUpdated())
                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    <label class="text-sm font-medium">Update Status:</label>
                    <select name="status" 
                            class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2">
                        @foreach(\App\Models\Order::getStatuses() as $key => $label)
                            <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">
                        Update
                    </button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Order Items --}}
            <div class="lg:col-span-2">
                <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Items</h2>
                    
                    @if($order->items->count() > 0)
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center space-x-4 p-4 bg-gray-800 rounded-lg">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ $item->product->image_url }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-16 h-16 rounded object-cover">
                                    @else
                                        <div class="w-16 h-16 bg-gray-700 rounded flex items-center justify-center">
                                            <span class="text-2xl">📦</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1">
                                        <h3 class="font-medium">{{ $item->product->name ?? 'Product' }}</h3>
                                        @if($item->product)
                                            <p class="text-sm text-gray-400">{{ $item->product->description }}</p>
                                        @endif
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-sm text-gray-400">Quantity: {{ $item->quantity }}</span>
                                            <span class="text-sm text-gray-400">Price: ${{ number_format($item->price, 2) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400">No items found for this order.</p>
                    @endif
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="space-y-6">
                {{-- Customer Information --}}
                @if($canManage)
                    <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                        <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-400">Name:</span> {{ $order->user->name }}</p>
                            <p><span class="text-gray-400">Email:</span> {{ $order->user->email }}</p>
                            @if($order->user->phone)
                                <p><span class="text-gray-400">Phone:</span> {{ $order->user->phone }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Order Summary --}}
                <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Items ({{ $order->getTotalItemsAttribute() }}):</span>
                            <span>${{ number_format($order->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr class="border-gray-700">
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total:</span>
                            <span>{{ $order->getFormattedTotalAttribute() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Order Timeline --}}
                <div class="bg-gray-900 rounded-lg border border-gray-700 p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Timeline</h2>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <div class="text-sm">
                                <p class="font-medium">Order Placed</p>
                                <p class="text-gray-400">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($order->status !== 'pending')
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <div class="text-sm">
                                    <p class="font-medium">Status: {{ $order->getStatusLabelAttribute() }}</p>
                                    <p class="text-gray-400">{{ $order->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mt-6 p-4 bg-green-600/10 text-green-300 border border-green-600/20 rounded">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>
@endsection