<div class="pt-24 pb-12 min-h-screen bg-black text-white">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8 text-center">Checkout - Test Version</h1>
        
        <div class="bg-white bg-opacity-10 backdrop-blur-md p-6 rounded-lg text-center">
            <p class="text-xl mb-4">This is a test checkout page</p>
            <p class="mb-4">Items in cart: {{ $itemCount }}</p>
            <p class="mb-4">Total: Rs. {{ number_format($total, 2) }}</p>
            
            @if($itemCount > 0)
                <p class="text-green-400">✅ Cart has items - checkout should work</p>
            @else
                <p class="text-red-400">❌ Cart is empty</p>
            @endif
            
            <div class="mt-6">
                <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>