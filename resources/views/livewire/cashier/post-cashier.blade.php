<div>
    <div x-data="{ 
        init() {
            // Watch for changes in customer data
            this.$watch('$wire.customers', (value) => {
                if (value && value.length) {
                    const currentData = {
                        customers: value,
                        activeCustomer: this.$wire.activeCustomer
                    };
                    localStorage.setItem('cashier_data', JSON.stringify(currentData));
                }
            });

            // Only load from localStorage if no customers exist
            this.$nextTick(() => {
                if (!$wire.customers.length) {
                    const savedData = localStorage.getItem('cashier_data');
                    if (savedData) {
                        const data = JSON.parse(savedData);
                        if (data.customers && data.customers.length) {
                            $wire.restoreCustomers(data);
                        }
                    }
                }
            });
        }
    }" class="relative">
        <!-- Customer Tabs with horizontal scroll -->
        <div class="sticky top-0 left-0 right-0 bg-white shadow-sm z-10">
            <div class="overflow-x-auto whitespace-nowrap px-4 py-2">
                <div class="inline-flex space-x-2">
                    @foreach($customers as $index => $customer)
                        <div class="inline-flex items-center">
                            <button wire:click="switchCustomer({{ $index }})"
                                class="inline-flex px-4 py-2 rounded-l-lg {{ $activeCustomer === $index ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                                {{ $customer['customerName'] ?: 'Pelanggan ' . ($index + 1) }}
                            </button>
                            @if(count($customers) > 1)
                                <button wire:click="deleteCustomer({{ $index }})"
                                    class="inline-flex px-2 py-2 {{ $activeCustomer === $index ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' }} rounded-r-lg border-l border-white/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach

                    <button wire:click="addNewCustomer"
                        class="inline-flex px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col md:flex-row">
            <!-- Customer Data Section - Shows first on mobile -->
            <div class="w-full md:w-[400px] p-4 bg-gray-50 md:order-2">
                <h2 class="text-xl font-bold mb-4">Data Pelanggan</h2>
                <div class="bg-white rounded-lg shadow p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Merk Mobil</label>
                        <input type="text" wire:model.defer="tempCustomerData.carType" placeholder="Masukan Jenis Mobil"
                            value="{{ $customers[$activeCustomer]['carType'] ?? '' }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                        <input type="text" wire:model.defer="tempCustomerData.carId" placeholder="Masukan Nomor Plat"
                            value="{{ $customers[$activeCustomer]['carId'] ?? '' }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Pembeli</label>
                        <div x-data="{
                            open: false,
                            search: '',
                            buyers: [],
                            init() {
                                this.$watch('search', value => {
                                    if (value.length > 2) {
                                        $wire.searchBuyer(value).then(result => {
                                            this.buyers = result;
                                            this.open = true;
                                        });
                                    } else {
                                        this.buyers = [];
                                        this.open = false;
                                    }
                                });
                            }
                        }" class="relative">
                            <input type="text" x-model="search" wire:model.defer="tempCustomerData.customerName"
                                placeholder="Masukkan nama pembeli" @focus="open = true"
                                @keydown.escape.window="open = false"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">

                            <div x-show="open && buyers.length > 0" x-transition @click.away="open = false"
                                class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="buyer in buyers" :key="buyer.id">
                                    <div @click="
                                            $wire.selectBuyer(buyer.id);
                                            search = buyer.name;
                                            open = false;
                                        " class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                        <div class="font-medium" x-text="buyer.name"></div>
                                        <div class="text-sm text-gray-600">
                                            <span x-text="buyer.car_number"></span>
                                            <span class="mx-1">-</span>
                                            <span x-text="buyer.phone_number"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                        <input type="text" wire:model.defer="tempCustomerData.customerPhone"
                            placeholder="Masukkan nomor WhatsApp"
                            value="{{ $customers[$activeCustomer]['customerPhone'] ?? '' }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    </div>

                    <button wire:click="saveCustomerData"
                        class="w-full py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Simpan Data Pelanggan
                    </button>

                    <button wire:click="printNota"
                        class="w-full py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cetak Nota
                    </button>

                    @error('print')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Products Section -->
            <div class="flex-1 p-4 bg-white md:order-1">
                <!-- Store Header - Hidden on mobile -->
                <div class="hidden md:block text-center mb-6 border-b-2 border-dashed border-gray-300 pb-4">
                    <h2 class="text-2xl font-bold">TOKO ELEKTRONIK MAJU JAYA</h2>
                    <p class="text-gray-600">Jl. Raya No. 123, Jakarta - Telp: 021-1234567</p>
                </div>

                <!-- Products Table with horizontal scroll on mobile -->
                <div class="overflow-x-scroll md:overflow-x-visible overflow-y-visible">
                    <table class="w-full mb-6 min-w-[800px]">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="w-[8%] py-2 text-left">Qty</th>
                                <th class="w-[42%] py-2 text-left">Nama Produk</th>
                                <th class="w-[15%] py-2 text-left">Modal</th>
                                <th class="w-[15%] py-2 text-left">Harga</th>
                                <th class="w-[15%] py-2 text-left">Total</th>
                                <th class="w-[5%] py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Input Row -->
                            <tr class="border-b border-gray-100">
                                <td class="py-2">
                                    <input type="number" wire:model="customers.{{ $activeCustomer }}.qty" min="1"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                </td>
                                <td class="py-2 relative">
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        selectedProduct: null,
                                        filteredProducts: [],
                                        selectedIndex: -1,
                                        init() {
                                            this.filteredProducts = $wire.products;
                                            this.$watch('search', value => {
                                                if (value === '') {
                                                    this.filteredProducts = $wire.products;
                                                } else {
                                                    this.filteredProducts = $wire.products.filter(product => 
                                                        product.title.toLowerCase().includes(value.toLowerCase())
                                                    );
                                                }
                                                this.selectedIndex = -1;
                                            });
                                        },
                                        selectProduct(product) {
                                            this.search = product.title;
                                            $wire.selectProduct(product.id);
                                            this.open = false;
                                        },
                                        onKeyDown(event) {
                                            if (!this.open) return;

                                            switch(event.key) {
                                                case 'ArrowDown':
                                                    event.preventDefault();
                                                    if (this.selectedIndex < this.filteredProducts.length - 1) {
                                                        this.selectedIndex++;
                                                        this.$refs.productsList.children[this.selectedIndex].scrollIntoView({
                                                            block: 'nearest'
                                                        });
                                                    }
                                                    break;
                                                case 'ArrowUp':
                                                    event.preventDefault();
                                                    if (this.selectedIndex > 0) {
                                                        this.selectedIndex--;
                                                        this.$refs.productsList.children[this.selectedIndex].scrollIntoView({
                                                            block: 'nearest'
                                                        });
                                                    }
                                                    break;
                                                case 'Enter':
                                                    event.preventDefault();
                                                    if (this.selectedIndex > -1 && this.filteredProducts[this.selectedIndex]) {
                                                        this.selectProduct(this.filteredProducts[this.selectedIndex]);
                                                    }
                                                    break;
                                                case 'Escape':
                                                    this.open = false;
                                                    break;
                                            }
                                        }
                                    }" @click.away="open = false" @keydown="onKeyDown($event)">
                                        <input type="text" x-model="search"
                                            wire:model="customers.{{ $activeCustomer }}.productName"
                                            @focus="open = true" @keydown.escape.window="open = false"
                                            placeholder="Cari atau ketik produk"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">

                                        <div x-show="open" x-transition
                                            class="absolute left-0 w-full bg-white rounded-md shadow-lg max-h-60 overflow-y-auto z-[9999]"
                                            style="top: calc(100% + 0.5rem);" x-ref="productsList">
                                            <template x-for="(product, index) in filteredProducts" :key="product.id">
                                                <div @click="selectProduct(product)" @mouseenter="selectedIndex = index"
                                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                                                    :class="{ 'bg-blue-50': selectedIndex === index }">
                                                    <div class="font-medium" x-text="product.title"></div>
                                                    <div class="text-sm text-gray-600">
                                                        <span>Stok: </span>
                                                        <span x-text="product.stock"></span>
                                                        <span class="ml-2">Modal: Rp</span>
                                                        <span x-text="product.modal.toLocaleString()"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="filteredProducts.length === 0"
                                                class="px-4 py-2 text-sm text-gray-500">
                                                Produk tidak ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Replace the Modal input -->
                                <td class="py-2">
                                    <div x-data="{ 
                                        formattedValue: '',
                                        actualValue: @entangle('customers.' . $activeCustomer . '.costPrice'),
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
                                        <div class="relative">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="text" x-model="formattedValue" @input="
                                                    const numbers = $event.target.value.replace(/[^0-9]/g, '');
                                                    actualValue = numbers;
                                                    formattedValue = formatNumber(numbers);
                                                "
                                                class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                                placeholder="0">
                                        </div>
                                    </div>
                                </td>

                                <!-- Replace the Harga input -->
                                <td class="py-2">
                                    <div x-data="{ 
                                        formattedValue: '',
                                        actualValue: @entangle('customers.' . $activeCustomer . '.sellPrice'),
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
                                        <div class="relative">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="text" x-model="formattedValue" @input="
                                                    const numbers = $event.target.value.replace(/[^0-9]/g, '');
                                                    actualValue = numbers;
                                                    formattedValue = formatNumber(numbers);
                                                "
                                                class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                                placeholder="0">
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 font-medium">Rp {{ number_format($this->calculateRowTotal()) }}</td>
                                <td class="py-2">
                                    <button wire:click="addItem"
                                        class="p-2 text-white bg-green-500 rounded-full hover:bg-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Add error message row -->
                            @error('stock')
                                <tr>
                                    <td colspan="6">
                                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                    </td>
                                </tr>
                            @enderror

                            <!-- Items List -->
                            @foreach($customers[$activeCustomer]['items'] as $index => $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2">{{ $item['qty'] }}</td>
                                    <td class="py-2">{{ $item['name'] }}</td>
                                    <td class="py-2">Rp {{ number_format($item['costPrice']) }}</td>
                                    <td class="py-2">Rp {{ number_format($item['sellPrice']) }}</td>
                                    <td class="py-2">Rp {{ number_format($item['total']) }}</td>
                                    <td class="py-2">
                                        <button wire:click="removeItem({{ $index }})"
                                            class="p-2 text-red-500 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h5 class="font-semibold mb-4">Ringkasan Pembelian</h5>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Item:</span>
                            <span class="font-medium">{{ $customers[$activeCustomer]['totalItems'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Modal:</span>
                            <span class="font-medium">Rp {{ number_format($totalCost) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Penjualan:</span>
                            <span class="font-medium">Rp {{ number_format($totalSell) }}</span>
                        </div>
                        <div class="flex justify-between text-green-600 font-semibold">
                            <span>Keuntungan:</span>
                            <span>Rp {{ number_format($profit) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hide scrollbar but keep functionality */
        .overflow-x-auto::-webkit-scrollbar {
            display: none;
        }

        .overflow-x-auto {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @push('scripts')
        <script>

        </script>
    @endpush
</div>