<div class="space-y-3">
  @guest
    <p class="text-sm">Please <a href="/login" class="underline">login</a> to manage your cart.</p>
  @else
    @foreach($items as $i)
      <div class="flex items-center justify-between rounded border p-3">
        <span>{{ $i->product->name ?? 'Deleted product' }} × {{ $i->quantity }}</span>
        <div class="flex gap-2">
          <button wire:click="decrement({{ $i->id }})" class="px-2 border">-</button>
          <button wire:click="increment({{ $i->id }})" class="px-2 border">+</button>
        </div>
      </div>
    @endforeach
    <div class="text-right font-bold">Total: LKR {{ number_format($total, 2) }}</div>
  @endguest
</div>
