<?php

namespace App\Providers;

use App\Libraries\Payments\ZarinPal\ZarinPal;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * استفاده از کلاس زرین‌پال
         */
        $this->app->singleton('ZarinPal', function () {
            $merchantID = config('services.zarinpal.merchantID', config('Zarinpal.merchantID', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'));

            $zarinpal = new ZarinPal($merchantID);

            if (config('services.zarinpal.sandbox', false)) {
                $zarinpal->enableSandbox();
            }
            if (config('services.zarinpal.zarinGate', false)) {
                $zarinpal->isZarinGate();
            }

            return $zarinpal;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
