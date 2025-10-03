<div x-data="{ 
        count: {{ $count }},
        updateCount(newCount) {
            this.count = newCount;
        }
     }"
     @cart-updated.window="updateCount($event.detail.count)"
     @cart-count-updated.window="updateCount($event.detail.count)"
     wire:key="cart-counter-{{ $count }}">
    <span x-show="count > 0" 
          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
        <span x-text="count > 99 ? '99+' : count"></span>
    </span>
</div>