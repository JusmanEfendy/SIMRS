<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // Detect the panel from the request URL path
        $path = $request->path();
        
        // Determine which panel we're logging out from
        if (str_starts_with($path, 'unit')) {
            return redirect('/unit/login');
        } elseif (str_starts_with($path, 'verifikator')) {
            return redirect('/verifikator/login');
        } elseif (str_starts_with($path, 'direksi')) {
            return redirect('/direksi/login');
        } elseif (str_starts_with($path, 'admin')) {
            return redirect('/admin/login');
        }
        
        // Default fallback to admin login
        return redirect('/admin/login');
    }
}
