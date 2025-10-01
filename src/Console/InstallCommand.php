<?php

namespace Rayhan2001\ModuleGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'module:install';
    protected $description = 'Install and configure Rayhan2001 Module Generator package';

    public function handle(Filesystem $files)
    {
        $this->info('⚙️  Module Generator installation');

        $type = $this->choice('Default module type?', ['api', 'web'], 0);

        $packageConfig = __DIR__ . '/../../config/module-generator.php';
        $appConfig = config_path('module-generator.php');

        if (!file_exists($appConfig)) {
            // copy package config to app
            if (!file_exists(dirname($appConfig))) {
                @mkdir(dirname($appConfig), 0755, true);
            }
            $contents = file_get_contents($packageConfig);
            $contents = str_replace("'default_type' => 'api'", "'default_type' => '$type'", $contents);
            file_put_contents($appConfig, $contents);
            $this->info("Config published to config/module-generator.php and default type set to: {$type}");
        } else {
            // update existing config value (safe replace)
            $current = file_get_contents($appConfig);
            if (strpos($current, "'default_type' =>") !== false) {
                $new = preg_replace("/'default_type'\s*=>\s*'([^']+)'/", "'default_type' => '$type'", $current);
                file_put_contents($appConfig, $new);
                $this->info("Config updated. Default type set to: {$type}");
            } else {
                $this->warn("Config file exists but doesn't contain 'default_type' setting. Please check manually.");
            }
        }

        $this->info('✅ Installation complete. You can now run: php artisan make:module Name --type=web|api');
    }
}
