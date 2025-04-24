@extends('layouts.main')

@section('title', 'Login')

@section('content')
    <form class="max-w-sm mx-auto" accept-charset="UTF-8" method="POST" action="{{ route('loginPost') }}">
        @csrf
        <div class="mb-5">
            <x-forms.input input-name="email" label="Email" value="{{ old('email') }}" type="email" required />
        </div>
        <div class="mb-5">
            <x-forms.input input-name="password" label="Password" type="password" required />
        </div>
        <div class="flex justify-between gap-x-2">
            <x-link-button href="/">Cancel</x-link-button>
            <x-forms.button color='blue'>Login</x-forms.button>
        </div>
    </form>
@endsection
