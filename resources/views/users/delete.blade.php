<x-app-layout>
    <x-slot name="header">{{ __('Confirm Delete') }}</x-slot>

    <div class="p-6 bg-white border-b border-gray-200">
        <p>Are you sure you want to delete user: <strong>{{ $user->name }}</strong>?</p>
        <form method="POST" action="{{ route('users.destroy', $user) }}" class="mt-4 flex gap-4">
            @csrf
            @method('DELETE')
            <x-primary-button>Confirm</x-primary-button>
            <a href="{{ route('users.index') }}" class="text-blue-500">Cancel</a>
        </form>
    </div>
</x-app-layout>
