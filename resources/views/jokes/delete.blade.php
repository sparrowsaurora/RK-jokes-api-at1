<x-app-layout>
    <x-slot name="header">
        {{ __('Delete Joke') }}
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <p class="mb-4 text-gray-700">Are you sure you want to move this joke to trash?</p>

            <h2 class="text-xl font-bold">{{ $joke->title }}</h2>
            <p class="mb-6">{{ $joke->content }}</p>

            <form method="POST" action="{{ route('jokes.destroy', $joke) }}">
                @csrf
                @method('DELETE')
                <x-primary-button class="bg-red-500 hover:bg-red-600">Yes, Delete</x-primary-button>
                <a href="{{ route('jokes.index') }}" class="ml-4 text-blue-500 hover:underline">Cancel</a>
            </form>
        </div>
    </div>
</x-app-layout>
