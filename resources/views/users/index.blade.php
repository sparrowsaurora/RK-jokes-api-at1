<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('users.index') }}" class="grow">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                {{ __('Users') }}
            </h2>
        </a>

        <a href="{{ route('users.create') }}"
            class="text-green-800 hover:text-green-100 bg-gray-100 hover:bg-green-800 border border-gray-300 rounded-lg transition ease-in-out duration-200 px-4 py-1">
            <i class="fa-solid fa-user-plus pr-1 aria-hidden:true"></i>
            New User
        </a>

        <form action="{{ route('users.index') }}" method="GET" class="flex flex-row gap-0 mt-2">
            <x-text-input id="search"
                type="text"
                name="search"
                class="border border-gray-200 rounded-r-none shadow-transparent"
                :value="$search ?? ''" />

            <button type="submit"
                class="text-gray-800 hover:text-gray-100 bg-gray-100 hover:bg-gray-800 border border-gray-300 rounded-lg transition ease-in-out duration-200 px-4 py-1 rounded-l-none">
                <i class="fa-solid fa-magnifying-glass pr-1 aria-hidden:true"></i>
                Search
            </button>
        </form>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <article class="my-0">
                    <header class="grid grid-cols-10 bg-gray-500 text-gray-50 text-lg px-4 py-2">
                        <span class="col-span-1">#</span>
                        <span class="col-span-4">User</span>
                        <span class="col-span-2">Added</span>
                        <span class="col-span-1">Role</span>
                        <span class="col-span-2">Actions</span>
                    </header>

                    @foreach ($users as $user)
                    <section class="px-4 grid grid-cols-10 py-1 hover:bg-gray-100 border-b border-b-gray-300 transition duration-150">
                        <p class="col-span-1">{{ $loop->iteration }}</p>

                        <div class="flex flex-col col-span-4 text-gray-800">
                            {{ $user->name }}
                            <small class="text-xs text-gray-400">{{ $user->email }}</small>
                        </div>

                        <p class="text-xs text-gray-400 col-span-2 p-1">
                            {{ $user->created_at->format('j M Y') }}
                        </p>

                        <p class="col-span-1">
                            <span class="text-xs bg-gray-800 text-gray-100 rounded-full px-2 py-0.5">
                                {{ $user->role ?? 'N/A' }}
                            </span>
                        </p>

                        <div class="col-span-2 flex border border-gray-300 rounded-lg px-0 overflow-hidden">
                            <a href="{{ route('users.show', $user) }}"
                                class="bg-gray-100 hover:bg-blue-500 text-blue-800 hover:text-gray-100 text-center border-r border-gray-300 transition duration-300 grow px-4 py-1.5 rounded-l">
                                <i class="fa-solid fa-user text-sm"></i> Show
                            </a>

                            <a href="{{ route('users.edit', $user) }}"
                                class="bg-gray-100 hover:bg-amber-500 text-amber-800 hover:text-gray-100 text-center border-x border-gray-300 transition duration-300 grow px-4 py-1.5">
                                <i class="fa-solid fa-user-pen text-sm"></i> Edit
                            </a>

                            <a href="{{ route('user.delete', $user) }}"
                                class="bg-gray-100 hover:bg-red-500 text-red-800 hover:text-gray-100 text-center border-l border-gray-300 transition duration-300 grow px-4 py-1.5 rounded-r">
                                <i class="fa-solid fa-user-minus text-sm"></i> Delete
                            </a>
                        </div>

                    </section>
                    @endforeach
                    @role('Administrator|Staff')
                    <footer class="px-4 pb-2 pt-4">
                        {{ $users->links() }}
                        <a href="{{ route('users.trash') }}" class="px-4 py-2 underline text-red-500">Trash</a>
                    </footer>
                    @endcan
                </article>

            </div>
        </div>
    </div>
</x-app-layout>