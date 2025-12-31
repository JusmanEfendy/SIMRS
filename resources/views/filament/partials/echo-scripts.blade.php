@auth
    <meta name="user-id" content="{{ auth()->id() }}">
@endauth
@vite(['resources/js/app.js'])

{{-- Custom Login Page Styles --}}
@if(request()->routeIs('filament.admin.auth.login') || request()->routeIs('filament.admin.auth.password-reset.*'))
    <link rel="stylesheet" href="{{ asset('css/filament-login.css') }}">
@endif
