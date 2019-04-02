<?php

namespace Sebastienheyd\Systempay;

use Blade;

class SystempayServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('systempay', function($var) {
            return '<?php $c='.(empty($var) ? '"systemPay"' : $var).'; $button = <<<HTML'.PHP_EOL;
        });

        Blade::directive('endsystempay', function() {
            return PHP_EOL.'HTML;'.PHP_EOL.'echo ${$c}->render($button); ?>';
        });

        $this->publishes([__DIR__.'/config/systempay.php' => config_path('systempay.php')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('systempay', Systempay::class);
    }
}
