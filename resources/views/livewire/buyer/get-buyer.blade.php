
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Data Pembeli</h2>
        <div class="w-1/3">
            <input 
                wire:model.live="search" 
                type="search" 
                placeholder="Cari pembeli..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th wire:click="sortBy('name')" class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer">
                        Nama Pembeli
                        @if ($sortField === 'name')
                            <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('phone_number')" class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer">
                        No. Telepon
                        @if ($sortField === 'phone_number')
                            <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('car_type')" class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer">
                        Merk Mobil
                        @if ($sortField === 'car_type')
                            <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('car_number')" class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer">
                        Plat Nomor
                        @if ($sortField === 'car_number')
                            <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('created_at')" class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer">
                        Tanggal Daftar
                        @if ($sortField === 'created_at')
                            <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($buyers as $buyer)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $buyer->name }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $buyer->phone_number }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $buyer->car_type }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $buyer->car_number }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $buyer->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Tidak ada data pembeli
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $buyers->links() }}
    </div>
</div>
