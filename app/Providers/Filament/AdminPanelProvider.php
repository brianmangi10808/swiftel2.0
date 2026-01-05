<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\Navigation\MenuItem;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Models\SmsGateway;
use App\Observers\SmsGatewayObserver;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Support\Facades\FilamentAsset;

use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;

class AdminPanelProvider extends PanelProvider
{
public function boot(): void
{
    FilamentAsset::register([
        Js::make('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js'),
        Js::make('live-traffic', asset('js/live-traffic.js')),
    ], 'swiftel-assets');
}

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'success' => Color::Green,
                'danger'  => Color::Red,
                   'dark' => '#006400'
            ])
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
          //  ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
         
      \App\Filament\Widgets\IspOverview::class,
       \App\Filament\Widgets\PaymentsChart::class,
       \App\Filament\Widgets\NewCustomersChart::class,
            \App\Filament\Widgets\CustomerLineStats::class,
        \App\Filament\Widgets\ActiveUsersByServiceChart::class,
            ])  
    ->userMenuItems([
                'settings' => MenuItem::make()
                    ->label('Settings')
                    ->url(fn () => \App\Filament\Pages\Settings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
                     'users' => MenuItem::make()
                    ->label('Users')
                           // ->url(fn () => route('filament.admin.pages.users')) // 🔥 CORRECT ROUTE

                    ->icon('heroicon-o-cog-6-tooth'),
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
                  \App\Http\Middleware\LogUserActivity::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
            
            
    }
}
