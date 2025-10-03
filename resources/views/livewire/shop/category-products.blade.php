<div class="space-y-4">
  <input class="border p-2 w-full" placeholder="Search…" wire:model.debounce.400ms="search" />
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($products as $p)
      <div class="rounded border p-3 bg-white shadow-md hover:shadow-lg transition-shadow">
        {{-- Product Image --}}
        <div class="w-full h-48 mb-3 overflow-hidden rounded">
          @if($p->image)
            <img src="{{ asset('storage/products/' . $p->image) }}" 
                 alt="{{ $p->name }}" 
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
          @else
            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
              <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
              </svg>
            </div>
          @endif
        </div>
        
        <h3 class="font-semibold text-lg mb-2">{{ $p->name }}</h3>
        <p class="text-sm text-gray-600 mb-3">{{ \Illuminate\Support\Str::limit($p->description, 80) }}</p>
        <div class="flex justify-between items-center">
          <span class="text-lg font-bold text-green-600">LKR {{ number_format($p->price, 2) }}</span>
          <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors" 
                  wire:click="$dispatch('add-to-cart', { id: {{ $p->product_id ?? $p->id }} })">
            Add to Cart
          </button>
        </div>
      </div>
    @endforeach
  </div>
  {{ $products->links() }}
</div>
