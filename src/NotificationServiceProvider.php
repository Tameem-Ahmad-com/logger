<?php

namespace Computan\Notification;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'computan');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'computan');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/notification.php', 'notification');

        // Register the service the package provides.
        $this->app->singleton('notification', function ($app) {
            return new Notification;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['notification'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
         //Export the migration
      if (! class_exists('CreateDebuggingLogTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/create_posts_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_debugging_logs_table.php'),
          // you can add any number of migrations here
        ], 'migrations');
      }
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/debugger.php' => config_path('debugger.php'),
          ], 'config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/computan'),
        ], 'notification.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/computan'),
        ], 'notification.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/computan'),
        ], 'notification.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
