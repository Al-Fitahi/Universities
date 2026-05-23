<?php

namespace App\Providers\Filament;
use Filament\Auth\Pages\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\CheckUniversityStatus;
use App\Filament\Pages\Auth\RegisterUniversity;
use App\Filament\Pages\Auth\LoginUniversity;
use App\Filament\Pages\Auth\EditUniversityProfile;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;




class UniversityPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('university')
            ->path('university')
            ->login(LoginUniversity::class)
            ->registration(RegisterUniversity::class)
            ->sidebarCollapsibleOnDesktop()
            ->resourceEditPageRedirect('view')
            ->passwordReset()
            ->emailVerification()
            ->emailChangeVerification()
            ->profile(EditUniversityProfile::class, $isSimple=false)
            ->multiFactorAuthentication([
              EmailAuthentication::make(),
              AppAuthentication::make(),
            ])
            ->authPasswordBroker('universitys')
            ->authGuard('university')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/University/Resources'), for: 'App\Filament\University\Resources')
            ->discoverPages(in: app_path('Filament/University/Pages'), for: 'App\Filament\University\Pages')
            ->pages([
     Dashboard::class,
    \App\Filament\University\Pages\UniversitySelectMajors::class,
    \App\Filament\University\Pages\UniversitySetConditions::class,

            ])
            ->discoverWidgets(in: app_path('Filament/University/Widgets'), for: 'App\Filament\University\Widgets')
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
                CheckUniversityStatus::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([

    ]);

    }
}



