<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;

class PostProduct extends Component
{
    public $title;
    public $category_id;
    public $modal;
    public $stock;
    public $editMode = false;
    public $categories;

    public function boot()
    {
        $this->categories = Category::all();
    }

    public function mount()
    {
        if (!$this->categories) {
            $this->categories = Category::all();
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required',
            'category_id' => 'required',
            'modal' => 'required|numeric',
            'stock' => 'required|numeric|min:0'
        ]);

        try {
            Product::create([
                'title' => $this->title,
                'category_id' => $this->category_id,
                'modal' => $this->modal,
                'stock' => $this->stock
            ]);

            $this->reset(['title', 'category_id', 'modal', 'stock']);
            $this->dispatch('product-saved');
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving product: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Ensure categories are always available
        if (!$this->categories) {
            $this->categories = Category::all();
        }

        return view('livewire.product.post-product');
    }
}
