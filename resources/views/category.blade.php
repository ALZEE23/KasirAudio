
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Category') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-100">
        <div class="space-y-4 px-6 sm:px-32 pt-10">
            <livewire:category.post-category />
            
        </div>
        <div class="space-y-4 px-6 sm:px-32 pt-10">
            <livewire:subcategory.post-subcategory />
        
        </div>
    </div>
    
    
</x-app-layout>