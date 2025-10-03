@extends('layouts.app')
@section('title', 'Track My Orders')

@section('content')
<div class="min-h-screen bg-black text-white pt-20">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-center mb-2">Track My Orders</h1>
            <p class="text-gray-400 text-center">Monitor your order status and delivery progress</p>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-gray-900 rounded-lg border border-gray-700 p-6 hover:border-blue-500 transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="mb-4 md:mb-0">
                                <div class="flex items-center space-x-4 mb-2">
                                    <h3 class="text-xl font-semibold">Order #{{ $order->order_id }}</h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $order->status === 'delivered' ? 'bg-green-600 text-green-100' : '' }}
                                        {{ $order->status === 'shipped' ? 'bg-blue-600 text-blue-100' : '' }}
                                        {{ $order->status === 'pending' ? 'bg-yellow-600 text-yellow-100' : '' }}
                                        {{ $order->status === 'paid' ? 'bg-purple-600 text-purple-100' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-600 text-red-100' : '' }}">
                                        {{ $order->getStatusLabelAttribute() }}
                                    </span>
                                </div>
                                <div class="text-gray-400 text-sm space-y-1">
                                    <p>Order Date: {{ $order->created_at->format('M d, Y') }}</p>
                                    <p>Total: {{ $order->getFormattedTotalAttribute() }}</p>
                                    @if($canManage)
                                        <p>Customer: {{ $order->user->name }} ({{ $order->user->email }})</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center transition-colors">
                                    View Details
                                </a>
                                
                                @if($canManage && $order->canBeUpdated())
                                    <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" 
                                                class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2">
                                            @foreach(\App\Models\Order::getStatuses() as $key => $label)
                                                <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if($order->items->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-700">
                                <h4 class="text-sm font-medium text-gray-400 mb-2">Order Items ({{ $order->getTotalItemsAttribute() }} items)</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="flex items-center space-x-3">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ $item->product->image_url }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="w-12 h-12 rounded object-cover">
                                            @else
                                                <div class="w-12 h-12 bg-gray-700 rounded flex items-center justify-center">
                                                    <span class="text-xs">📦</span>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <p class="text-sm font-medium">{{ $item->product->name ?? 'Product' }}</p>
                                                <p class="text-xs text-gray-400">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($order->items->count() > 3)
                                    <p class="text-xs text-gray-400 mt-2">+ {{ $order->items->count() - 3 }} more items</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📦</div>
                <h3 class="text-xl font-semibold mb-2">No Orders Found</h3>
                <p class="text-gray-400 mb-6">You haven't placed any orders yet.</p>
                <a href="{{ route('home') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-block transition-colors">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection