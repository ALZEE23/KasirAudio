<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Produk') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-100 mb-24">
        <div class="space-y-4 px-4 md:px-8 lg:px-32 pt-6 md:pt-10">
            
            <livewire:product.get-product />
        </div>
    </div>
</x-app-layout>