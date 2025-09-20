<?php

namespace Rayhan2001\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Generate a full CRUD module with Controller, Repository, Requests, Views, Migration & Routes';

    public function handle()
    {
        $name = $this->argument('name');
        $studly = ucfirst($name);
        $lower = strtolower($name);
        $plural = str($name)->plural()->studly();   // Example: Category -> Categories
        $pluralLower = strtolower($plural);         // Example: categories

        // Paths
        $controllerPath = app_path("Http/Controllers/{$studly}Controller.php");
        $repositoryPath = app_path("Repositories/{$studly}Repository.php");
        $storeRequestPath = app_path("Http/Requests/{$studly}Request.php");
        $updateRequestPath = app_path("Http/Requests/Update{$studly}Request.php");
        $modelPath = app_path("Models/{$studly}.php");
        $migrationPath = database_path("migrations/" . date('Y_m_d_His') . "_create_{$pluralLower}_table.php");
        $viewsPath = resource_path("views/{$pluralLower}");
        $webRoutePath = base_path("routes/web.php");
        $apiRoutePath = base_path("routes/api.php");

        // Make directories if not exists
        if (!File::exists(dirname($controllerPath))) File::makeDirectory(dirname($controllerPath), 0755, true);
        if (!File::exists(dirname($repositoryPath))) File::makeDirectory(dirname($repositoryPath), 0755, true);
        if (!File::exists(dirname($storeRequestPath))) File::makeDirectory(dirname($storeRequestPath), 0755, true);
        if (!File::exists($viewsPath)) File::makeDirectory($viewsPath, 0755, true);

        // Generate files from stubs
        $this->generateFile(__DIR__ . '/../stubs/controller.stub', $controllerPath, $studly, $lower, $plural, $pluralLower);
        $this->generateFile(__DIR__ . '/../stubs/repository.stub', $repositoryPath, $studly, $lower, $plural, $pluralLower);
        $this->generateFile(__DIR__ . '/../stubs/request.store.stub', $storeRequestPath, $studly, $lower, $plural, $pluralLower);
        $this->generateFile(__DIR__ . '/../stubs/request.update.stub', $updateRequestPath, $studly, $lower, $plural, $pluralLower);
        $this->generateFile(__DIR__ . '/../stubs/model.stub', $modelPath, $studly, $lower, $plural, $pluralLower);
        $this->generateFile(__DIR__ . '/../stubs/migration.stub', $migrationPath, $studly, $lower, $plural, $pluralLower);

        // Views
        $this->generateFile(__DIR__ . '/../stubs/views/index.stub', $viewsPath . "/index.blade.php", $studly, $lower, $plural, $pluralLower);
        $this->generateFile(__DIR__ . '/../stubs/views/create.stub', $viewsPath . "/create.blade.php", $studly, $lower, $plural, $pluralLower);
        $this->generateFile(__DIR__ . '/../stubs/views/edit.stub', $viewsPath . "/edit.blade.php", $studly, $lower, $plural, $pluralLower);

        // Append routes
        $webRoute = $this->getStubContent(__DIR__ . '/../stubs/web.stub', $studly, $lower, $plural, $pluralLower);
        File::append($webRoutePath, "\n" . $webRoute);

        $apiRoute = $this->getStubContent(__DIR__ . '/../stubs/api.stub', $studly, $lower, $plural, $pluralLower);
        File::append($apiRoutePath, "\n" . $apiRoute);

        $this->info("✅ {$studly} Module generated successfully!");
    }

    private function generateFile($stubPath, $targetPath, $studly, $lower, $plural, $pluralLower)
    {
        $content = $this->getStubContent($stubPath, $studly, $lower, $plural, $pluralLower);
        File::put($targetPath, $content);
    }

    private function getStubContent($stubPath, $studly, $lower, $plural, $pluralLower)
    {
        $content = File::get($stubPath);
        return str_replace(
            ['{{studly}}', '{{lower}}', '{{plural}}', '{{pluralLower}}'],
            [$studly, $lower, $plural, $pluralLower],
            $content
        );
    }
}
