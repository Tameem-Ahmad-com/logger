<?php

namespace JohnDoe\BlogPackage\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallDebugger extends Command
{
    protected $signature = 'debugger:install';

    protected $description = 'Install the Package';

    public function handle()
    {
        $this->info('Installing Debugger Package...');

        $this->info('Publishing configuration...');

        if (! $this->configExists('debugger.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        $this->info('Installation Done');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Computan\Debugger\DebuggerServiceProvider",
            '--tag' => "config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

       $this->call('vendor:publish', $params);
    }
}