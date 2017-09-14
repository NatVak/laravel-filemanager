<?php

namespace Ybaruchel\LaravelFileManager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade as Form;
use Collective\Html\HtmlServiceProvider;
use Tightenco\Ziggy\ZiggyServiceProvider;
use Intervention\Image\ImageServiceProvider;
use Ybaruchel\LaravelFileManager\Providers\FacadesServiceProvider;
use Ybaruchel\LaravelFileManager\Providers\ValidatorServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(ImageServiceProvider::class);
        $this->app->register(FacadesServiceProvider::class);
        $this->app->register(ValidatorServiceProvider::class);
        $this->app->register(ZiggyServiceProvider::class);

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
        $this->publishes([$configPath => config_path('file-manager.php')], 'file-manager-config');

        // Translations loading
        $this->loadTranslationsFrom(__DIR__.'/translations', 'file_manager');

        // Migrations loading
        $this->loadMigrationsFrom(__DIR__.'/migrations');
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
            ['name', 'defaultImage', 'allowedExtensions' => [], 'maxFileSize', 'fields' => null, 'croppable' => false, 'cropName' => false, 'attributes' => []]);

        Form::component('bsMultiImage', 'FileManager::components.multiimage',
            ['name', 'defaultImages' => [], 'allowedExtensions' => [], 'maxFileSize', 'minFiles', 'maxFiles', 'fields' => null, 'clonerField' => null, 'croppable' => false,  'cropName' => false, 'attributes' => []]);

        Form::component('bsFile', 'FileManager::components.file',
            ['name', 'defaultFile', 'allowedExtensions' => [], 'maxFileSize', 'fields' => null, 'attributes' => []]);

        Form::component('bsMultiFile', 'FileManager::components.multifile',
            ['name', 'defaultFiles' => [], 'allowedExtensions' => [], 'maxFileSize', 'minFiles', 'maxFiles', 'fields' => null, 'attributes' => []]);
    }
}