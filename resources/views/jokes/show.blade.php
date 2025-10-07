<x-app-layout>
    <x-slot name="header">
        {{ $joke->title }}
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            @foreach($joke->categories as $category)
                <span class="badge">{{ $category->name }}</span>
            @endforeach

            <h2 class="text-2xl font-bold mb-4">{{ $joke->title }}</h2>
            <p class="text-gray-700">{{ $joke->content }}</p>

            @auth
                <form method="POST" action="{{ route('jokes.react', $joke) }}" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="like">
                    <button type="submit" class="{{ $joke->likes()->where('user_id', auth()->id())->exists() ? 'text-green-500' : '' }}">
                        ▲ Like ({{ $joke->likes()->count() }})
                    </button>
                </form>

                <form method="POST" action="{{ route('jokes.react', $joke) }}" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="dislike">
                    <button type="submit" class="{{ $joke->dislikes()->where('user_id', auth()->id())->exists() ? 'text-red-500' : '' }}">
                        ▼ Dislike ({{ $joke->dislikes()->count() }})
                    </button>
                </form>

                @if ($joke->reactions()->where('user_id', auth()->id())->exists())
                    <form method="POST" action="{{ route('jokes.unreact', $joke) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-500"><sub>(Remove Reaction)</sub></button>
                    </form>
                @endif
            @endauth


            <div class="mt-4">
                <a href="{{ route('jokes.edit', $joke) }}" class="text-blue-600 hover:underline">Edit</a> |
                <a href="{{ route('jokes.index') }}" class="text-gray-600 hover:underline">Back to List</a>
            </div>
        </div>
    </div>
</x-app-layout>
