<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class GetProduct extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $sortField = 'title';
    public $sortDirection = 'asc';
    public $categories;

    protected $listeners = [
        'product-saved' => '$refresh',
        'editProduct' => 'editProduct',
        'deleteProduct' => 'deleteProduct'
    ];

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function editProduct($id)
    {
        $this->dispatch('edit-product', id: $id);
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            session()->flash('message', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus produk.');
        }
    }

    public function render()
    {
        return view('livewire.product.get-product', [
            'products' => Product::query()
                ->when($this->search, function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->when($this->categoryFilter, function ($query) {
                    $query->where('category_id', $this->categoryFilter);
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10)
        ]);
    }
}
