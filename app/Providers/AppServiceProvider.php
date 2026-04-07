<?php

namespace App\Providers;

use App\Models\StreetNumberingPlate;
use App\Models\AddressIndexingRequest;
use App\Models\StreetApplication;
use App\Models\Street;
use App\Models\Payment;
use App\Observers\StreetNumberingPlateRequestObserver;
use App\Observers\StreetNumberingPlateRequestEmailObserver;
use App\Observers\AddressIndexingRequestObserver;
use App\Observers\StreetApplicationObserver;
use App\Observers\StreetObserver;
use App\Observers\PaymentObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \App\Services\InAppNotificationService::class,
            \App\Services\InAppNotificationService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerObservers();
    }

    /**
     * Register model observers for SMS notifications
     */
    protected function registerObservers(): void
    {
        // SMS Notifications
        StreetNumberingPlate::observe(StreetNumberingPlateRequestObserver::class);
        AddressIndexingRequest::observe(AddressIndexingRequestObserver::class);
        StreetApplication::observe(StreetApplicationObserver::class);

        // Email Notifications
        StreetNumberingPlate::observe(StreetNumberingPlateRequestEmailObserver::class);

        // In-app Notifications
        Street::observe(StreetObserver::class);
        Payment::observe(PaymentObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
