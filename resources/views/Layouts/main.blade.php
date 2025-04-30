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
    <div>
        <nav class="flex flex-col sm:flex-row justify-between items-center gap-2 border-b-2 border-white/25 p-2">
            <a href="{{ route('books.index') }}">
                <img loading="lazy" style="width:50px;" src="{{ Vite::asset('resources/images/logo.jpg') }}"
                    alt="logo" />
            </a>
            <div class="flex justify-center items-center gap-2 flex-wrap">
                <x-nav-link name='authors.index'>Authors</x-nav-link>
                @auth
                    <x-nav-link name='books.create'>Add Book</x-nav-link>
                    @if ($book_count = Auth::user()->books()->count())
                        <x-nav-link name='books.owner'>Your Books ({{ $book_count }})</x-nav-link>
                    @endif
                @endauth
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

        <main class="p-4">
            <div class="mx-auto max-w-7xl">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('footer')
</body>

</html>
