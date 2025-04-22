<?php

declare(strict_types=1);

namespace OneBit\Captcha;

use Illuminate\Support\ServiceProvider;
use OneBit\Captcha\Contracts\Captcha as CaptchaContract;
use OneBit\Captcha\Services\Captcha;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/1bitcaptcha.php', '1bitcaptcha'
        );

        $this->app->bind(CaptchaContract::class, Captcha::class);

        $this->app->singleton('1bitcaptcha', function ($app) {
            return $app->make(CaptchaContract::class);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/1bitcaptcha.php' => config_path('1bitcaptcha.php'),
        ], '1bitcaptcha-config');

        // Publish font
        $this->publishes([
            __DIR__ . '/../resources/font' => public_path('vendor/1bitcaptcha/font'),
        ], '1bitcaptcha-assets');
    }
}
