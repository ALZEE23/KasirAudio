<div class="p-6">
    <h2 class="text-lg font-semibold mb-6">{{ $editMode ? 'Edit' : 'Tambah' }} Produk</h2>

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                <input type="text" wire:model="title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

            <div x-data="{ 
                formattedValue: '',
                actualValue: @entangle('modal'),
                formatNumber(val) {
                    if (!val) return '';
                    return new Intl.NumberFormat('id-ID').format(val);
                },
                init() {
                    this.formattedValue = this.formatNumber(this.actualValue);
                    this.$watch('actualValue', value => {
                        this.formattedValue = this.formatNumber(value);
                    });
                }
            }">
                <label class="block text-sm font-medium text-gray-700">Modal</label>
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="text" x-model="formattedValue" @input="
                            const numbers = $event.target.value.replace(/[^0-9]/g, '');
                            actualValue = numbers;
                            formattedValue = formatNumber(numbers);
                        "
                        class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="0">
                </div>
                @error('modal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Stok</label>
                <input type="number" wire:model="stock"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="button" @click="showModal = false"
                class="mr-2 px-4 py-2 text-sm text-gray-600 hover:text-gray-700">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                {{ $editMode ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>