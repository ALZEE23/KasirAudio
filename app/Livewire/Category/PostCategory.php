<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class PostCategory extends Component
{
    use WithPagination;

    public $category_name;
    public $editMode = false;
    public $categoryId;

    protected $rules = [
        'category_name' => 'required|min:3|unique:categories,category_name'
    ];

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $category = Category::find($this->categoryId);
            $category->update(['category_name' => $this->category_name]);
            $this->dispatch('category-updated');
        } else {
            Category::create(['category_name' => $this->category_name]);
            $this->dispatch('category-created');
        }

        $this->reset(['category_name', 'editMode', 'categoryId']);
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $this->categoryId = $category->id;
        $this->category_name = $category->category_name;
        $this->editMode = true;
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        $this->dispatch('category-deleted');
    }

    public function render()
    {
        return view('livewire.category.post-category', [
            'categories' => Category::orderBy('category_name')->paginate(10)
        ]);
    }
}
