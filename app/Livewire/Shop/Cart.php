<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use Livewire\Component;

class Cart extends Component
{
    protected $listeners = ['add-to-cart' => 'add'];

    public function add($payload = []): void
    {
        abort_unless(auth()->check(), 403);
        
        // Log for debugging
        \Log::info('Cart add method called', ['payload' => $payload]);
        
        if (empty($payload) || !isset($payload['id'])) {
            \Log::info('Invalid payload, dispatching cartUpdated anyway');
            $this->dispatch('cartUpdated');
            return;
        }

        $quantity = isset($payload['qty']) ? (int)$payload['qty'] : 1;
        $quantity = max(1, $quantity); // Ensure minimum quantity is 1

        $item = CartItem::firstOrNew([
            'user_id'    => auth()->id(),
            'product_id' => $payload['id'],
        ]);

        if ($item->exists) {
            $item->quantity += $quantity;
        } else {
            $item->quantity = $quantity;
        }
        
        $item->save();
        
        \Log::info('Cart item saved', ['item_id' => $item->cart_id, 'quantity' => $item->quantity]);
        
        // Dispatch the cartUpdated event to refresh cart counter
        $this->dispatch('cartUpdated');
        \Log::info('cartUpdated event dispatched');
    }

    public function increment(int $id): void
    {
        CartItem::whereKey($id)->increment('quantity');
        $this->dispatch('cartUpdated');
    }

    public function decrement(int $id): void
    {
        $item = CartItem::findOrFail($id);
        $item->quantity > 1 ? $item->decrement('quantity') : $item->delete();
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        $items = auth()->check()
            ? CartItem::with('product')->where('user_id', auth()->id())->get()
            : collect();

        $total = $items->sum(fn($i) => $i->quantity * ($i->product->price ?? 0));
        return view('livewire.shop.cart', compact('items','total'));
    }
}
