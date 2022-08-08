<?php

namespace Toproplus\Export;

use Illuminate\Support\ServiceProvider;

class ExportServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Export $extension)
    {
        if (! Export::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'export');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/toproplus/laravel-admin-ext-export')],
                'export'
            );
        }

        $this->app->booted(function () {
            Export::routes(__DIR__.'/../routes/web.php');
        });
    }
}