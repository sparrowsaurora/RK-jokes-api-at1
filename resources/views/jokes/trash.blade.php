<x-app-layout>
    <x-slot name="header">
        {{ __('Trashed Jokes') }}
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        <div class="flex justify-between mb-4">
            <form method="POST" action="{{ route('jokes.restoreAll') }}">
                @csrf @method('POST')
                <x-primary-button class="bg-green-500 hover:bg-green-600">Recover All</x-primary-button>
            </form>

            <form method="POST" action="{{ route('jokes.forceDeleteAll') }}">
                @csrf @method('DELETE')
                <x-primary-button class="bg-red-500 hover:bg-red-600" onclick="return confirm('Delete all permanently?')">Empty Trash</x-primary-button>
            </form>
        </div>

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
                            <form method="POST" action="{{ route('jokes.restore', $joke->id) }}" class="inline">
                                @csrf @method('POST')
                                <button class="text-green-600 hover:underline">Recover</button>
                            </form> |
                            <form method="POST" action="{{ route('jokes.forceDelete', $joke->id) }}" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Permanently delete?')" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
