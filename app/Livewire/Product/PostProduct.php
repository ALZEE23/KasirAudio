<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;

class PostProduct extends Component
{
    public $title;
    public $modal;
    public $stock;
    public $category_id;

    public $categories;
    public $editMode = false;
    public $productId;

    protected $rules = [
        'title' => 'required|min:3',
        'modal' => 'required|numeric|min:0',
        'stock' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id'
    ];

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $product = Product::find($this->productId);
            $product->update([
                'title' => $this->title,
                'modal' => $this->modal,
                'stock' => $this->stock,
                'category_id' => $this->category_id
            ]);
            $this->dispatch('product-updated');
        } else {
            Product::create([
                'title' => $this->title,
                'modal' => $this->modal,
                'stock' => $this->stock,
                'category_id' => $this->category_id
            ]);
            $this->dispatch('product-created');
        }

        $this->reset(['title', 'modal', 'stock', 'category_id', 'editMode', 'productId']);
    }

    public function edit($id)
    {
        $product = Product::find($id);
        $this->productId = $product->id;
        $this->title = $product->title;
        $this->modal = $product->modal;
        $this->stock = $product->stock;
        $this->category_id = $product->category_id;
        $this->editMode = true;
    }

    public function render()
    {
        return view('livewire.product.post-product');
    }
}
