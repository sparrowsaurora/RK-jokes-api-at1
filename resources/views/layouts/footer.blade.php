<footer class="mt-4  p-4 md:p-8
             bg-gray-900 dark:bg-gray-950
             text-gray-300 dark:text-gray-400
               text-sm
               grid grid-cols-1 md:grid-cols-2 gap-2">
    <section class="">
        <p>&copy; Copyright 2025 TAFE/Ryan Kelley. All rights reserved.</p>
        <br>
        @role('Administrator|Staff')
        <a href="{{ route('users.index') }}">Users</a>
        @endcan
    </section>
    <section class="grid grid-cols-2 gap-4">
        <nav class="flex flex-col gap-1">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('jokes.index') }}">Jokes</a>
            <a href="{{ route('about') }}">About</a>
        </nav>
        <nav class="flex flex-col gap-1">
            <a href="https://github.com/sparrowsaurora/RK-saas-fed-at2-pt2/blob/main/License.md">License</a>
            <a href="{{ route('contact') }}">Contact Us</a>
            <a href="{{ route('privacy') }}">Privacy</a>

        </nav>
    </section>
</footer>