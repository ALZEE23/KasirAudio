<div x-data="{ 
        init() {
            this.$nextTick(() => {
                // Load saved data on init
                const savedData = localStorage.getItem('cashier_data')
                if (savedData) {
                    const data = JSON.parse(savedData)
                    this.$wire.restoreCustomers(data)
                }
            })

            // Watch for changes and save
            this.$watch('$wire.customers', (value) => {
                if (value) {
                    localStorage.setItem('cashier_data', JSON.stringify({
                        customers: value,
                        activeCustomer: this.$wire.activeCustomer
                    }))
                }
            })
        }
    }" class="relative">
    <!-- Customer Tabs -->
    <div class="sticky top-0 left-0 right-0 bg-white shadow-sm z-10 px-6 py-2">
        <div class="flex items-center space-x-2 overflow-x-auto">
            @foreach($customers as $index => $customer)
                <button wire:click="switchCustomer({{ $index }})"
                    class="px-4 py-2 rounded-lg {{ $activeCustomer === $index ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                    {{ $customer['customerName'] ?: 'Pelanggan ' . ($index + 1) }}
                </button>
            @endforeach

            <button wire:click="addNewCustomer" class="px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>

            <button wire:click="clearData" class="px-4 py-2 text-sm text-red-600 hover:text-red-700">
                Clear All Data
            </button>
        </div>
    </div>

    <!-- Main Content - Adjusted padding -->
    <div class="flex flex-1">
        <!-- Left Section - Nota Pembelian -->
        <div class="flex-1 p-6 bg-white border-r border-gray-200">
            <div class="text-center mb-6 border-b-2 border-dashed border-gray-300 pb-4">
                <h2 class="text-2xl font-bold">TOKO ELEKTRONIK MAJU JAYA</h2>
                <p class="text-gray-600">Jl. Raya No. 123, Jakarta - Telp: 021-1234567</p>
            </div>

            <!-- Nota Table -->
            <table class="w-full mb-6">
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
                        <td class="py-2">
                            <input type="text" wire:model="customers.{{ $activeCustomer }}.productName"
                                placeholder="Nama produk"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        </td>
                        <td class="py-2">
                            <input type="number" wire:model="customers.{{ $activeCustomer }}.costPrice"
                                placeholder="Modal"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        </td>
                        <td class="py-2">
                            <input type="number" wire:model="customers.{{ $activeCustomer }}.sellPrice"
                                placeholder="Harga"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
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

                    <!-- Items List -->
                    @foreach($customers[$activeCustomer]['items'] as $index => $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ $item['qty'] }}</td>
                            <td class="py-2">{{ $item['name'] }}</td>
                            <td class="py-2">Rp {{ number_format($item['costPrice']) }}</td>
                            <td class="py-2">Rp {{ number_format($item['sellPrice']) }}</td>
                            <td class="py-2">Rp {{ number_format($item['total']) }}</td>
                            <td class="py-2">
                                <button wire:click="removeItem({{ $index }})" class="p-2 text-red-500 hover:text-red-600">
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

            <!-- Summary Card -->
            <div class="bg-white rounded-lg shadow p-6">
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

        <!-- Right Section -->
        <div class="w-[400px] p-6 bg-gray-50">
            <h2 class="text-2xl font-bold mb-6">Data Pelanggan</h2>

            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Merk Mobil</label>
                    <input type="text" wire:model="customers.{{ $activeCustomer }}.carType"
                        placeholder="Masukan Jenis Mobil"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                    <input type="text" wire:model="customers.{{ $activeCustomer }}.carId"
                        placeholder="Masukan Nomor Plat"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Pembeli</label>
                    <input type="text" wire:model="customers.{{ $activeCustomer }}.customerName"
                        placeholder="Masukkan nama pembeli"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                    <input type="tel" wire:model="customers.{{ $activeCustomer }}.customerPhone"
                        placeholder="Masukkan nomor WhatsApp"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                    <input type="datetime-local" wire:model="transactionDate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    <select wire:model="paymentMethod"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="credit">Kredit</option>
                    </select>
                </div>

                @if($paymentMethod === 'qris')
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <!-- QR Code placeholder -->
                        <div class="w-48 h-48 mx-auto bg-white border-2 border-gray-200 rounded-lg"></div>
                        <p class="mt-2 text-sm text-gray-600">Scan QR code untuk pembayaran</p>
                        <div class="mt-2 flex justify-between text-sm">
                            <span>Total Pembayaran:</span>
                            <span class="font-medium">Rp {{ number_format($totalSell) }}</span>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea wire:model="notes" rows="2" placeholder="Catatan tambahan"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"></textarea>
                </div>

                <button wire:click="printNota"
                    class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Nota
                </button>

                @error('print')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Remove the previous event listener code since we're now using Alpine.js
    </script>
@endpush