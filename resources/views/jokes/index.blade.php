<x-app-layout>
    <x-slot name="header">
        {{ __('All Jokes') }}
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        <div class="flex justify-end mb-4">
            <a href="{{ route('jokes.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">New Joke</a>
        </div>
        <form method="GET" action="{{ route('jokes.index') }}" class="mb-4">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">-- All Categories --</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </form>
        <p>Total Categories: {{ $categoriesTotal }}</p>

        <table class="w-full bg-white shadow rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jokes as $joke)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $joke->title }}</td>
                    <td class="px-4 py-2 text-center">
                        | <a href="{{ route('jokes.show', $joke) }}" class="text-blue-600 hover:underline">Show</a> |
                        @can('update', $joke)
                        <a href="{{ route('jokes.edit', $joke) }}" class="text-amber-600 hover:underline">Edit</a> |
                        @endcan
                        @can('delete', $joke)
                        <form action="{{ route('jokes.destroy', $joke) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Move to trash?')" class="text-red-600 hover:underline">Delete</button> |
                        </form>
                            @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $jokes->links() }}
            @role('Administrator|Staff')
            <a href="{{ route('jokes.trash') }}" class="px-4 py-2 underline text-red-500">Trash</a>
            @endcan
        </div>

    </div>
</x-app-layout>
