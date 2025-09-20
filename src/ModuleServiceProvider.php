<?php

namespace Rayhan2001\ModuleGenerator;

use Illuminate\Support\ServiceProvider;
use Rayhan2001\ModuleGenerator\Console\InstallCommand;
use Rayhan2001\ModuleGenerator\Console\MakeModuleCommand;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/module-generator.php', 'module-generator');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // publish config
            $this->publishes([
                __DIR__ . '/../config/module-generator.php' => config_path('module-generator.php'),
            ], 'config');

            // register commands
            $this->commands([
                InstallCommand::class,
                MakeModuleCommand::class,
            ]);
        }
    }
}
