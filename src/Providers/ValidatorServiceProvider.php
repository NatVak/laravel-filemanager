<?php

namespace Ybaruchel\LaravelFileManager\Providers;

use Intervention\Image\Facades\Image;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('is_croppable', function ($attribute, $value, $parameters, $validator) {
            if (!count($parameters)) {
                abort(401, 'Please enter custom crop field for validation');
            }
            $cropArrayName = $parameters[0];
            $DS = DIRECTORY_SEPARATOR;
            $currentDir = public_path('uploads'.$DS.'original');
            $img = $currentDir.$DS.$value;

            if (!is_file($img))
                return false;

            if (!$cropArrayName || !is_string($cropArrayName))
                return false;

            $cropArray = config('file-manager.custom_crops.'.$cropArrayName);

            if (!is_array($cropArray) || empty($cropArray))
                return false;

            $image = Image::make($img);

            // Image data
            $imgWidth  = $image->width();
            $imgHeight = $image->height();

            $imageRatio = $imgWidth / $imgHeight;

            // run on each crop
            foreach ($cropArray as $cropKey => $cropData) {

                // store the crop size in vars
                $targ_w = $cropData['width'];
                $targ_h = $cropData['height'];

                // if crop width bigger then the image width or the height is bigger then the image height continue to the next crop
                if ($targ_w > $imgWidth || $targ_h > $imgHeight)
                    return false;

                // If the crop matches the exact ratio like the image + 0.03 -
                // Just resize to the crop size
                $currentCropRatio = $targ_w / $targ_h;

                if (!(round($imageRatio,2) < (round($currentCropRatio,2) + 0.03) && round($imageRatio,2) > (round($currentCropRatio,2) - 0.03))) {
                    // get the width and height ration between the image and the crop
                    $widthRatio  = $imgWidth / $targ_w;
                    $heightRatio = $imgHeight / $targ_h;

                    // get the smallest
                    $smallestRatio = min ($widthRatio, $heightRatio );

                    if($smallestRatio == $widthRatio)
                        $finalRatio = $imgWidth / $targ_w;
                    else
                        $finalRatio = $imgHeight / $targ_h;

                    $targ_w = $targ_w * $finalRatio;
                    $targ_h = $targ_h * $finalRatio;

                    $targ_x = (($imgWidth - $targ_w) / 2);
                    $targ_y = (($imgHeight - $targ_h) / 2);

                    if(($targ_w + $targ_x) > $imgWidth)
                        return false;

                    if(($targ_h + $targ_y) > $imgHeight)
                        return false;
                }
            }
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}