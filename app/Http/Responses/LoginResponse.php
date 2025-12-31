<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        return match (true) {
        $user->hasRole('Admin')       => redirect('/admin'),
        $user->hasRole('Verifikator') => redirect('/verifikator'),
        $user->hasRole('Direksi')     => redirect('/direksi'),
        $user->hasRole('Unit')        => redirect('/unit'),
        default => abort(403, 'Role tidak dikenali'),
        };
    }
}
