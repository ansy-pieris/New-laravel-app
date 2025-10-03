<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProducts extends Component
{
    use WithPagination;

    public Category $category;
    public string $search = '';

    public function mount(string $slug): void
    {
        $this->category = Category::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $products = Product::where('category_id', $this->category->id)
            ->when($this->search, fn($q) => $q->where('name','like',"%{$this->search}%"))
            ->latest()->paginate(12);

        return view('livewire.shop.category-products', compact('products'));
    }
}
