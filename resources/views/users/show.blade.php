<x-app-layout>
    <x-slot name="header">
        {{ __('User Details') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ $user->name }}</h2>

                <div class="space-y-2 text-gray-700">
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Created At:</strong> {{ $user->created_at->format('j M Y, H:i') }}</p>
                    <p><strong>City:</strong> {{ $user->city ?? '—' }}</p>
                    <p><strong>State:</strong> {{ $user->state ?? '—' }}</p>
                    <p><strong>Given Name:</strong> {{ $user->given_name ?? '—' }}</p>
                    <p><strong>Family Name:</strong> {{ $user->family_name ?? '—' }}</p>
                    <p><strong>Last Login:</strong> {{ $user->login_at ?? 'Never' }}</p>
                    <p><strong>Last Logout:</strong> {{ $user->logout_at ?? 'N/A' }}</p>
                </div>

                <div class="mt-6 flex gap-4">
                    <a href="{{ route('users.edit', $user) }}"
                       class="bg-amber-500 hover:bg-amber-600 text-white font-medium px-4 py-2 rounded transition duration-200">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>

                    <a href="{{ route('user.delete', $user) }}"
                       class="bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded transition duration-200">
                        <i class="fa-solid fa-user-minus"></i> Delete
                    </a>

                    <a href="{{ route('users.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded transition duration-200">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
