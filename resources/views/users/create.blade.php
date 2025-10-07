<x-app-layout>
    <x-slot name="header">
        {{ __('Add New User') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-4">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />

                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />

                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />

                        <x-input-label for="given_name" :value="__('Given Name')" />
                        <x-text-input id="given_name" name="given_name" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('given_name')" class="mt-2" />

                        <x-input-label for="family_name" :value="__('Family Name')" />
                        <x-text-input id="family_name" name="family_name" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('family_name')" class="mt-2" />

                        <x-input-label for="city" :value="__('City')" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />

                        <x-input-label for="state" :value="__('State')" />
                        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('state')" class="mt-2" />

                        <div class="mt-4 flex gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('users.index') }}" class="text-blue-500 hover:underline">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
