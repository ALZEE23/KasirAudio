
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select wire:model="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Sub Kategori</label>
                    <div class="flex gap-4">
                        <input type="text" wire:model="title" placeholder="Nama Sub Kategori"
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            {{ $editMode ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kategori
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sub Kategori
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($subcategories as $subcategory)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $subcategory->category->category_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $subcategory->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $subcategory->id }})" 
                                class="text-blue-600 hover:text-blue-900 mr-3">
                                Edit
                            </button>
                            <button wire:click="delete({{ $subcategory->id }})"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus sub kategori ini?')"
                                class="text-red-600 hover:text-red-900">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $subcategories->links() }}
    </div>
</div>
