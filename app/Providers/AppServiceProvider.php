<?php

namespace App\Providers;

use App\Models\StreetNumberingPlate;
use App\Models\AddressIndexingRequest;
use App\Models\StreetApplication;
use App\Observers\StreetNumberingPlateRequestObserver;
use App\Observers\StreetNumberingPlateRequestEmailObserver;
use App\Observers\AddressIndexingRequestObserver;
use App\Observers\StreetApplicationObserver;
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
        //
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
