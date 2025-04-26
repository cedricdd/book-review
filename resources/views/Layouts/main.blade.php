<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    @vite(['resources/js/app.js'])

    @yield('header')
</head>
<body class="min-h-full bg-dark text-white pb-20">
    <div class="p-6">
        <nav class="flex justify-between items-center border-b-2 border-white/25 py-2">
            <a href="{{ route('books.index') }}">
                <img loading="lazy" style="width:50px;" src="{{ Vite::asset("resources/images/logo.jpg") }}" alt="logo" />
            </a>
            <div>
                <x-nav-link name='books.index'>Books</x-nav-link>
            </div>
            <div class="flex items-center gap-1">
                @auth
                    <x-nav-link name='users.profile' :parameters="Auth::user()->id">Profile</x-nav-link>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-forms.button>Logout</x-forms.button>
                    </form>
                @endauth
                @guest
                <x-nav-link name='login'>Login</x-nav-link>
                @endguest
            </div>
        </nav>

        <x-flash-message name="failure" />
        <x-flash-message name="success" />

        <main class="mt-10">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('footer')
</body>
</html>