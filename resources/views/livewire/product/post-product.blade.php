<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-6">{{ $editMode ? 'Edit' : 'Tambah' }} Produk</h2>
    
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
            <input type="text" wire:model="title" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Modal</label>
            <input type="number" wire:model="modal" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            @error('modal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Stok</label>
            <input type="number" wire:model="stock" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Kategori</label>
            <select wire:model="category_id" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>
            @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            @if($editMode)
                <button type="button" wire:click="$set('editMode', false)" 
                    class="mr-2 px-4 py-2 text-sm text-gray-600 hover:text-gray-700">
                    Batal
                </button>
            @endif
            <button type="submit" 
                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                {{ $editMode ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>
