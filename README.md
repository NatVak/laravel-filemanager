# Laravel 5 FileManager - Deprecated!


========


## Installation

### Laravel >= 5.5:
Require this package with composer:

```php
composer require ybaruchel/laravel-filemanager
```
That's it :)
### Laravel <= 5.4:
Require this package with composer:

```php
composer require ybaruchel/laravel-filemanager
```
After updating composer, add the ServiceProvider to the providers array in config/app.php

```php
Ybaruchel\LaravelFileManager\FileManagerServiceProvider::class,
```
If you want to use the facade, add this to your facades in app.php:

```php
'FileManager' => Ybaruchel\LaravelFileManager\Support\Facades\FileManager::class,
```

### Configuration:
Register package's routes in the routes service provider file:
```php
FileManager::routes();
```

Publish the config file using:
```php
php artisan vendor:publish --tag="file-manager-config"
```

Include the tightenco/ziggy js routes putting the routes generator on your layout file (in the ```<head>``` tag):
```php
@routes
```

For including the package assets add the next files on your layout template:
```php
// Inside the <head> tag
@include('FileManager::partials.styles')

// Before closing the <body> tag
@include('FileManager::partials.scripts')
```

* Make sure you choose a disk on the package configuration file - Files will be stored on that disk.

