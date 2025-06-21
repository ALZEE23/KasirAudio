<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Livewire\Component;

class PostProduct extends Component
{
    public $productId;
    public $title;
    public $category_id;
    public $subcategory_id;
    public $stock;
    public $modal;
    public $editMode = false;
    public $categories;
    public $subcategories = [];

    protected $rules = [
        'title' => 'required',
        'category_id' => 'required',
        'subcategory_id' => 'required',
        'stock' => 'required|numeric|min:0',
        'modal' => 'required|numeric|min:0'
    ];

    protected $listeners = ['edit-product' => 'editProduct'];

    public function boot()
    {
        $this->categories = Category::all();
    }

    public function updatedCategoryId($value)
    {
        if (!empty($value)) {
            $this->subcategories = Subcategory::where('category_id', $value)->get();
        } else {
            $this->subcategories = [];
        }
        $this->subcategory_id = ''; // Reset subcategory when category changes
    }

    public function mount()
    {
        $this->categories = Category::all();
        if ($this->category_id) {
            $this->updatedCategoryId($this->category_id);
        }
    }

    public function editProduct($id)
    {
        $this->editMode = true;
        $this->productId = $id;
        $product = Product::findOrFail($id);

        $this->title = $product->title;
        $this->category_id = $product->category_id;
        $this->subcategory_id = $product->subcategory_id;
        $this->stock = $product->stock;
        $this->modal = $product->modal;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $product = Product::findOrFail($this->productId);
                $product->update([
                    'title' => $this->title,
                    'category_id' => $this->category_id,
                    'subcategory_id' => $this->subcategory_id,
                    'stock' => $this->stock,
                    'modal' => $this->modal
                ]);
            } else {
                Product::create([
                    'title' => $this->title,
                    'category_id' => $this->category_id,
                    'subcategory_id' => $this->subcategory_id,
                    'stock' => $this->stock,
                    'modal' => $this->modal
                ]);
            }

            $this->reset();
            $this->dispatch('product-saved');
            session()->flash('message', $this->editMode ? 'Produk berhasil diupdate.' : 'Produk berhasil ditambahkan.');
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan produk.');
        }
    }

    public function resetForm()
    {
        $this->reset(['productId', 'title', 'category_id', 'subcategory_id', 'stock', 'modal', 'editMode']);
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
