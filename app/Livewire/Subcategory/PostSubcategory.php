<?php

namespace App\Livewire\Subcategory;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PostSubcategory extends Component
{
    use WithPagination;

    public $title;
    public $category_id;
    public $subcategoryId;
    public $editMode = false;

    protected $rules = [
        'title' => 'required|min:3',
        'category_id' => 'required|exists:categories,id'
    ];

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $subcategory = Subcategory::find($this->subcategoryId);
            $subcategory->update([
                'title' => $this->title,
                'category_id' => $this->category_id,
                'slug' => Str::slug($this->title)
            ]);
        } else {
            Subcategory::create([
                'title' => $this->title,
                'category_id' => $this->category_id,
                'slug' => Str::slug($this->title)
            ]);
        }

        $this->reset(['title', 'category_id', 'subcategoryId', 'editMode']);
    }

    public function edit($id)
    {
        $subcategory = Subcategory::find($id);
        $this->subcategoryId = $subcategory->id;
        $this->title = $subcategory->title;
        $this->category_id = $subcategory->category_id;
        $this->editMode = true;
    }

    public function delete($id)
    {
        Subcategory::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.subcategory.post-subcategory', [
            'categories' => Category::all(),
            'subcategories' => Subcategory::with('category')->paginate(10)
        ]);
    }
}
