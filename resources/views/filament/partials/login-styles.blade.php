{{-- Custom Login Page Styles for all panels --}}
@if(request()->is('*/login') || request()->is('*/password-reset/*'))
    <link rel="stylesheet" href="{{ asset('css/filament-login.css') }}">
@endif
