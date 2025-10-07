<x-app-layout>
    <x-slot name="header">
        {{ __('Edit Joke') }}
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('jokes.update', $joke) }}">
                @csrf
                @method('PUT')

                <x-input-label for="title" :value="__('Title')" />
                <x-text-input name="title" value="{{ old('title', $joke->title) }}" class="block w-full mt-1 mb-4" required />

                <x-input-label for="content" :value="__('Content')" />
                <textarea name="content" class="block w-full mt-1 mb-4 border-gray-300 rounded" rows="5" required>{{ old('content', $joke->content) }}</textarea>

                @foreach ($categories as $category)
                    <label>
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', $joke->categories->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                        {{ $category->name }}
                    </label>
                @endforeach

                <x-primary-button>{{ __('Update') }}</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
