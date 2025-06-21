<div class="p-6">
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{ 
        showModal: false,
        showDeleteModal: false,
        productToDelete: null,
        init() {
            Livewire.on('close-modal', () => {
                this.showModal = false;
            });
            Livewire.on('product-saved', () => {
                this.showModal = false;
            });
            Livewire.on('edit-product', () => {
                this.showModal = true;
            });
        }
    }">
        <!-- Header & Button -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Management Stok Barang</h2>
            <button @click="showModal = true"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk
            </button>
        </div>

        <!-- Modal -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <!-- Modal Content -->
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div @click.away="showModal = false" class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
                   
                    <livewire:product.post-product />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="text-center">
                        <svg class="mx-auto mb-4 w-14 h-14 text-red-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>

                        <h3 class="mb-5 text-lg font-normal text-gray-700">
                            Apakah anda yakin ingin menghapus produk ini?
                        </h3>

                        <div class="flex justify-center gap-4">
                            <button @click="
                                $wire.deleteProduct(productToDelete);
                                showDeleteModal = false;
                                productToDelete = null;
                            " class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Ya, Hapus
                            </button>
                            <button @click="
                                showDeleteModal = false;
                                productToDelete = null;
                            " class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <select wire:model.live="categoryFilter"
                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="subCategoryFilter"
                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Semua Sub Kategori</option>

            </select>

            <input wire:model.live="search" type="search" placeholder="Cari produk..."
                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Product Table -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('id')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            No</th>
                        <th wire:click="sortBy('category_id')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            Kategori</th>
                        <th wire:click="sortBy('sub_category_id')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            Sub Kategori</th>
                        <th wire:click="sortBy('title')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            Nama Produk</th>
                        <th wire:click="sortBy('stock')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            Banyaknya</th>
                        <th wire:click="sortBy('modal')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            Modal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->category->category_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->subCategory->title ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->stock }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($product->modal) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp
                                {{ number_format($product->stock * $product->modal) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <button wire:click="editProduct({{ $product->id }})"
                                        class="p-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="
                                                showDeleteModal = true;
                                                productToDelete = {{ $product->id }};"
                                        class="p-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data produk
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>