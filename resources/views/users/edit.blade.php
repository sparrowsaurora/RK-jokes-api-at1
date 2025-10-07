<x-app-layout>
    <x-slot name="header">
        {{ __('Edit User') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-4">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="mt-1 block w-full" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />

                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        <x-input-label for="given_name" :value="__('Given Name')" />
                        <x-text-input id="given_name" name="given_name" type="text" value="{{ old('given_name', $user->given_name) }}" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('given_name')" class="mt-2" />

                        <x-input-label for="family_name" :value="__('Family Name')" />
                        <x-text-input id="family_name" name="family_name" type="text" value="{{ old('family_name', $user->family_name) }}" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('family_name')" class="mt-2" />

                        <x-input-label for="city" :value="__('City')" />
                        <x-text-input id="city" name="city" type="text" value="{{ old('city', $user->city) }}" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />

                        <x-input-label for="state" :value="__('State')" />
                        <x-text-input id="state" name="state" type="text" value="{{ old('state', $user->state) }}" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('state')" class="mt-2" />

                        <div class="mt-4 flex gap-4">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                            <a href="{{ route('users.index') }}" class="text-blue-500 hover:underline">Cancel</a>
                        </div>
                    </div>
                </form>
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
