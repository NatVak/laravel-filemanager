<?php

namespace Ybaruchel\LaravelFileManager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade as Form;
use Collective\Html\HtmlServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        require __DIR__.'/../vendor/autoload.php';

        $this->loadViewsFrom(__DIR__.'/views', 'FileManager');

        $this->registerHelpers();

        $this->registerDependencies();

        $this->registerFormComponents();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Config loading
        $configPath = __DIR__ . '/../config/file-manager.php';
        $this->publishes([$configPath => config_path('file-manager.php')], 'laravel-file-manager-config');

        // Translations loading
        $this->loadTranslationsFrom(__DIR__.'/translations', 'file_manager');

        // Migrations loading
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        // Routes loading
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    private function registerDependencies()
    {
        $this->app->register(HtmlServiceProvider::class);
        AliasLoader::getInstance(['Form'=>'\Collective\Html\FormFacade']);
    }

    /**
     * Register helpers file
     */
    private function registerHelpers()
    {
        // Helpers file loading
        if (file_exists($file = __DIR__ . '/Support/helpers.php')) {
            require $file;
        }
    }

    /**
     * Registers the form components
     */
    private function registerFormComponents()
    {
        Form::component('bsImage', 'FileManager::components.image',
            ['name', 'cropName' => false, 'defaultImage', 'allowedExtensions' => [], 'maxFileSize', 'fields' => null, 'croppable' => true, 'attributes' => []]);

        Form::component('bsMultiImage', 'FileManager::components.multiimage',
            ['name', 'cropName' => false, 'defaultImages' => [], 'allowedExtensions' => [], 'maxFileSize', 'minFiles', 'maxFiles', 'fields' => null, 'clonerField' => null, 'croppable' => true, 'attributes' => []]);

        Form::component('bsFile', 'FileManager::components.file',
            ['name', 'defaultFile', 'allowedExtensions' => [], 'maxFileSize', 'fields' => null, 'attributes' => []]);

        Form::component('bsMultiFile', 'FileManager::components.multifile',
            ['name', 'defaultFiles' => [], 'allowedExtensions' => [], 'maxFileSize', 'minFiles', 'maxFiles', 'fields' => null, 'attributes' => []]);
    }
}