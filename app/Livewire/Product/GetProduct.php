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
