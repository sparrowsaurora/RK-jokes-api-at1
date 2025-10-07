<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 gap-6 flex flex-col">

                    <h2 class="text-3xl">
                        <a href="{{ route('jokes.index') }}" class="text-blue-500 hover:underline">See more</a>
                    </h2>
                    @if ($randomJoke)
                    <div class="mt-4 bg-gray-100 p-4 rounded shadow">
                        <h3 class="text-xl font-semibold">{{ $randomJoke->title }}</h3>
                        <p class="mt-2 text-gray-700">{{ $randomJoke->content }}</p>
                    </div>
                    @else
                    <div>No jokes available yet!</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>