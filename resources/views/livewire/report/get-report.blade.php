
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-4">Laporan Penjualan</h2>
        
        <!-- Report Type Selector -->
        <div class="flex gap-4 mb-4">
            <label class="inline-flex items-center">
                <input type="radio" wire:model.live="reportType" value="daily" class="form-radio">
                <span class="ml-2">Laporan Harian</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" wire:model.live="reportType" value="monthly" class="form-radio">
                <span class="ml-2">Laporan Bulanan</span>
            </label>
        </div>

        <!-- Date Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            @if($reportType === 'daily')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" wire:model.live="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                    <input type="date" wire:model.live="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Bulan</label>
                    <input type="month" wire:model.live="selectedMonth" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Total Penjualan</h3>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['total_sales']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Total Modal</h3>
            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($summary['total_capital']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Total Profit</h3>
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($summary['total_profit']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Total Transaksi</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $summary['total_transactions'] }}</p>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    @if($reportType === 'daily')
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembeli</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($report as $date => $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reportType === 'daily')
                                {{ $data['date'] }}
                            @else
                                {{ $date }}
                            @endif
                        </td>
                        @if($reportType === 'daily')
                            <td class="px-6 py-4 whitespace-nowrap">{{ $data['buyer'] }}</td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            Rp {{ number_format($reportType === 'daily' ? $data['total_buy'] : $data['total_sales']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            Rp {{ number_format($reportType === 'daily' ? $data['capital'] : $data['total_capital']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            Rp {{ number_format($reportType === 'daily' ? ($data['total_buy'] - $data['capital']) : $data['total_profit']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $reportType === 'daily' ? $data['quantity'] : $data['total_quantity'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data untuk periode ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
