@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Checkout</h1>

    @if ($errors->any())
        <div class="mb-4 border border-red-300 bg-red-50 p-3 rounded">
            <ul class="list-disc ml-5 text-sm text-red-700">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Shipping form -->
        <div class="md:col-span-2 border rounded p-4">
            <form method="POST" action="{{ route('checkout.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Full Name</label>
                    <input name="shipping_name" value="{{ old('shipping_name', $defaults['shipping_name']) }}"
                           class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input name="shipping_phone" value="{{ old('shipping_phone', $defaults['shipping_phone']) }}"
                           class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Address</label>
                    <input name="shipping_address" value="{{ old('shipping_address', $defaults['shipping_address']) }}"
                           class="w-full border rounded p-2" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">City</label>
                        <input name="shipping_city" value="{{ old('shipping_city', $defaults['shipping_city']) }}"
                               class="w-full border rounded p-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Postal Code</label>
                        <input name="shipping_postal" value="{{ old('shipping_postal', $defaults['shipping_postal']) }}"
                               class="w-full border rounded p-2" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Place Order
                    </button>
                </div>
            </form>
        </div>

        <!-- Order summary -->
        <div class="border rounded p-4">
            <h2 class="font-semibold mb-3">Order Summary</h2>
            <div class="space-y-2 max-h-64 overflow-auto">
                @forelse ($items as $i)
                    <div class="flex justify-between text-sm">
                        <span>
                            {{ $i->product->name ?? 'Deleted product' }}
                            <span class="text-gray-500">× {{ $i->quantity }}</span>
                        </span>
                        <span>LKR {{ number_format(($i->product->price ?? 0) * $i->quantity, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Your cart is empty.</p>
                @endforelse
            </div>
            <div class="border-t mt-3 pt-3 flex justify-between font-semibold">
                <span>Total</span>
                <span>LKR {{ number_format($total, 2) }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                Adjust quantities on the <a class="underline" href="{{ route('cart') }}">cart page</a>.
            </p>
        </div>
    </div>
</div>
@endsection
