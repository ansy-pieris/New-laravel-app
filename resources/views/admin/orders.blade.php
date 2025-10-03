@extends('layouts.app')

@section('title', 'Manage Orders - ' . ucfirst($routePrefix ?? 'Admin'))

@section('content')
<div class="bg-black min-h-screen pt-24">
    <main class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6 text-white">Manage Orders</h1>

        <!-- Orders Table -->
        <section class="bg-white p-6 rounded shadow">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-black">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center text-white font-semibold">Order ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-center text-white font-semibold">Customer Name</th>
                        <th class="border border-gray-300 px-4 py-2 text-center text-white font-semibold">Order Date</th>
                        <th class="border border-gray-300 px-4 py-2 text-center text-white font-semibold">Status</th>
                        <th class="border border-gray-300 px-4 py-2 text-center text-white font-semibold">Update Status</th>
                        <!-- <th class="border border-gray-300 px-4 py-2 text-center">Actions</th> -->
                    </tr>
                </thead>
                <tbody id="orders-tbody">
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'text-yellow-600',
                                    'processing' => 'text-blue-600',
                                    'shipped' => 'text-indigo-600',
                                    'delivered' => 'text-green-600',
                                    'cancelled' => 'text-red-600',
                                    default => 'text-gray-600',
                                };
                            @endphp
                            <tr data-order-id="{{ $order->order_id }}">
                                <td class="border border-gray-300 px-4 py-2 text-center text-gray-800 font-medium">{{ $order->order_id }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center text-gray-800">{{ $order->user_name }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center text-gray-600">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="border border-gray-300 px-4 py-2 font-semibold {{ $statusClass }} status-cell">
                                    {{ ucfirst($order->status) }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <select class="status-dropdown border border-gray-300 rounded px-2 py-1 text-gray-800 bg-white" data-order-id="{{ $order->order_id }}">>
                                        <option value="">Select Status</option>
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </td>
                                <!-- <td class="border border-gray-300 px-4 py-2 text-center">
                                    <button type="button" class="view-details-btn bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700" data-order-id="{{ $order->order_id }}">
                                        View Details
                                    </button>
                                </td> -->
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-600">No orders found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </section>
    </main>
</div>

<script>
console.log("Manage Orders JS loaded");

document.addEventListener('DOMContentLoaded', function () {
    // Update status
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('status-dropdown')) {
            const orderId = e.target.getAttribute('data-order-id');
            const newStatus = e.target.value;
            if (newStatus && confirm('Are you sure you want to update the status?')) {
                updateOrderStatus(orderId, newStatus);
            }
        }
    });

    function updateOrderStatus(orderId, newStatus) {
        fetch('{{ route($routePrefix . ".orders.updateStatus") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId, status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                const statusCell = row.querySelector('.status-cell');
                statusCell.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                statusCell.className = 'border border-gray-300 px-4 py-2 font-semibold status-cell ' + getStatusClass(newStatus);
                alert('Order status updated successfully!');
            } else {
                alert('Failed to update order status.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update order status.');
        });
    }

    function getStatusClass(status) {
        const classes = {
            'pending': 'text-yellow-600',
            'processing': 'text-blue-600',
            'shipped': 'text-indigo-600',
            'delivered': 'text-green-600',
            'cancelled': 'text-red-600'
        };
        return classes[status] || 'text-gray-600';
    }
});
</script>
@endsection