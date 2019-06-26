# Edited by Nati

#Comments

As you'll read this is a deprecated library so we just make it work.
we added metronic CSS file to this package.
in order to upload files successfully you should do two things:

1) Create an `upload` folder in your public directory.
2) If you like to use `rtl` direction please make an entry in your `app.php` config file like so:
`'direction' => 'rtl'`

Good advice: add `/public/uploads/*` to your `.gitignore`

Good Luck!!!


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
If you want to use the facades, add this to your facades in app.php:

```php
'FileManager' => Ybaruchel\LaravelFileManager\Support\Facades\FileManager::class,
'Cropper' => Ybaruchel\LaravelFileManager\Support\Facades\Cropper::class,
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

For including the package assets add the next lines to your layout template:
```php
// Inside the <head> tag
<meta name="csrf-token" content="{{ csrf_token() }}">
// And
@include('FileManager::partials.styles')

// Before closing the <body> tag
<script>
    $(function () {
        FileManagerModal.init();
    });
</script>
@include('FileManager::partials.scripts')
@include('FileManager::partials.file-manager-modal')
```

Add the crop validation translation to your validation.php translation file:
```php
'is_croppable' => 'The :attribute is not croppable according to all sizes.',
```

Prepare a minimalist layout for popup state, then add it to the file-manager.php config file.


* Make sure you choose a disk on the package configuration file - Files will be stored on that disk.

