<?php 
namespace Computan\Debugger;

use Illuminate\Support\ServiceProvider;
use JohnDoe\BlogPackage\Console\InstallDebugger;

class DebuggerServiceProvider extends ServiceProvider
{
  public function boot()
  {
    if ($this->app->runningInConsole()) {
        // $this->commands([
        //     InstallDebugger::class,
        // ]);
        
      //Export the migration
      if (! class_exists('CreateDebuggingLogTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/create_posts_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_debugging_logs_table.php'),
          // you can add any number of migrations here
        ], 'migrations');
      }
      /* publishing the config file */
      $this->publishes([
        __DIR__.'/../config/debugger.php' => config_path('debugger.php'),
      ], 'config');


      //Schedule the command if we are using the application via the CLI
      if ($this->app->runningInConsole()) {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('some:command')->everyMinute();
        });
    }

    }
  }
}