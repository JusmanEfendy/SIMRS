<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->brandLogo(asset('images/logo-kemenkes.png'))
            ->brandLogoHeight('3rem')
            ->path('admin')
            ->authGuard('web')
            ->login()
            ->passwordReset()
            ->colors([
                'primary' => Color::hex('#d6a2f8ff'),
            ])
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->userMenuItems([
                UserMenuItem::make('profile')
                    ->label(fn () => auth()->user()->name)
                    // ->url(fn () => Filament::getProfileUrl())
                    ->icon('heroicon-o-user-circle'),
                ])
            ->authMiddleware([
                Authenticate::class,
                'role:Admin',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->databaseNotifications(
                condition: fn () => auth()->check() && !auth()->user()->hasRole('Admin')
            )
            ->databaseNotificationsPolling('5s')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): View => view('filament.partials.echo-scripts')
            );
    }
}

