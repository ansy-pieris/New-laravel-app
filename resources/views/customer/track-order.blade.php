@extends('layouts.guest')
@section('title', $canManage ? 'Manage Orders' : 'My Orders')

@php
  $statusClass = function($s) {
    return match($s) {
      'pending'    => 'text-yellow-600',
      'processing' => 'text-blue-600',
      'shipped'    => 'text-indigo-600',
      'delivered'  => 'text-green-600',
      'cancelled'  => 'text-red-600',
      default      => 'text-gray-600',
    };
  };
@endphp

@section('content')
  @if (session('success'))
    <div class="max-w-7xl mx-auto mt-24 mb-4 px-6 py-3 rounded bg-green-600/10 text-green-300 border border-green-600/20">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div class="max-w-7xl mx-auto mt-24 mb-4 px-6 py-3 rounded bg-red-600/10 text-red-300 border border-red-600/20">
      {{ session('error') }}
    </div>
  @endif

  <main class="max-w-7xl mx-auto p-6 mt-6">
    <h1 class="text-3xl font-semibold mb-6">{{ $canManage ? 'Manage Orders' : 'My Orders' }}</h1>

    <section class="bg-white text-gray-900 p-6 rounded shadow overflow-x-auto">
      <table class="min-w-full border-collapse border border-gray-300">
        <thead>
          <tr class="bg-gray-50">
            <th class="border border-gray-300 px-4 py-2 text-center">Order ID</th>
            @if($canManage)
              <th class="border border-gray-300 px-4 py-2 text-center">Customer Name</th>
            @endif
            <th class="border border-gray-300 px-4 py-2 text-center">Order Date</th>
            <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
            @if($canManage)
              <th class="border border-gray-300 px-4 py-2 text-center">Update</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td class="border border-gray-300 px-4 py-2 text-center font-medium">
                #{{ $order->order_id }}
              </td>

              @if($canManage)
                <td class="border border-gray-300 px-4 py-2 text-center">
                  {{ $order->user->name ?? '—' }}
                </td>
              @endif

              <td class="border border-gray-300 px-4 py-2 text-center">
                {{ optional($order->created_at)->format('Y-m-d') }}
              </td>

              <td class="border border-gray-300 px-4 py-2 text-center font-semibold {{ $statusClass($order->status) }}">
                {{ ucfirst($order->status) }}
              </td>

              @if($canManage)
                <td class="border border-gray-300 px-4 py-2 text-center">
                  <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="inline-flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status"
                            class="border rounded px-2 py-1 text-sm"
                            onchange="this.form.submit()">
                      @foreach (['pending','processing','shipped','delivered','cancelled'] as $st)
                        <option value="{{ $st }}" @selected($order->status === $st)>{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                    <noscript>
                      <button type="submit" class="text-sm px-3 py-1 rounded bg-black text-white">Update</button>
                    </noscript>
                  </form>
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ $canManage ? 5 : 3 }}" class="text-center p-4 text-gray-500">
                No orders found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-4">
        {{ $orders->links() }}
      </div>
    </section>
  </main>
@endsection
