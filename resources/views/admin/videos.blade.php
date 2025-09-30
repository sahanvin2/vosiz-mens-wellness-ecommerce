<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Video Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <livewire:video-management />
            </div>
        </div>
    </div>
</x-app-layout>