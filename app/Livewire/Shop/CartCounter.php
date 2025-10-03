<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use Livewire\Component;

class CartCounter extends Component
{
    public $count = 0;
    
    protected $listeners = ['cartUpdated' => 'refreshCount'];

    public function mount()
    {
        $this->refreshCount();
    }
    
    public function refreshCount()
    {
        $this->count = auth()->check() 
            ? CartItem::where('user_id', auth()->id())->sum('quantity')
            : 0;

        \Log::info('CartCounter refreshed', ['count' => $this->count, 'user_id' => auth()->id()]);
        
        // Force component re-render
        $this->render();
        
        // Dispatch browser event to update Alpine.js
        $this->dispatch('cart-count-updated', count: $this->count);
    }

    public function render()
    {
        return view('livewire.shop.cart-counter');
    }
}