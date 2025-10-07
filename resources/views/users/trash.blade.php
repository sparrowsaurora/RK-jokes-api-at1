<x-app-layout>
    <x-slot name="header">{{ __('Trashed Users') }}</x-slot>

    <div class="p-6 bg-white border-b border-gray-200">
        <form method="POST" action="{{ route('users.recover-all') }}" class="inline">
            @csrf
            @method('PATCH')
            <button class="text-green-600" onclick="return confirm('Recover all trashed users?')">Recover All</button>
        </form>
        <form method="POST" action="{{ route('users.empty-all') }}" class="inline">
            @csrf @method('DELETE')
            <button class="text-red-600" onclick="return confirm('Delete all trashed users permanently?')">Empty Trash</button>
        </form>

        <table class="mt-4 w-full">
            <thead><tr><th>Name</th><th>Email</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form method="POST" action="{{ route('users.recover-one', $user->id) }}">
                            @csrf @method('PATCH')
                            <button class="text-blue-500">Recover</button>
                        </form>
                        <form method="POST" action="{{ route('users.empty-one', $user->id) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-500" onclick="return confirm('Permanently delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
