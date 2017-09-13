<?php

namespace Ybaruchel\LaravelFileManager\Services\Cropper;

use Ybaruchel\LaravelFileManager\Services\Services;

class CropperService extends Services
{
    /**
     * Method for auto cropping images, this method can receive
     * a single image or an array of images
     *
     * @param $images
     * @param $cropKey
     * @param bool $forceCrop - Ignore if the image is not size compatible
     * @return mixed
     */
    public static function crop($images, $cropKey, $forceCrop = false)
    {
        // Forcing array
        $images = (array) $images;

        // Invoke of cropping method
        foreach ($images as $image) {
            self::doCrop($image, $cropKey, $forceCrop);
        }
    }

    /**
     * Crops a single image
     * @param $image
     * @param $cropKey
     * @param bool $forceCrop - Ignore if the image is not size compatible
     * @return bool
     */
    private static function doCrop($image, $cropKey, $forceCrop)
    {
        return self::autoCrop(null, $image, $cropKey, $forceCrop);
    }

    /**
     * Do the auto crop
     *
     * @param null $sourcePath
     * @param $img
     * @param $cropArrayName
     * @param bool $forceCrop - Ignore if the image is not size compatible
     * @return bool
     */
    private static function autoCrop($sourcePath = NULL, $img, $cropArrayName, $forceCrop)
    {
        $DS = DIRECTORY_SEPARATOR;
        $saveFolder = public_path('uploads'.$DS.'crop');
        if (!is_dir($saveFolder))
            mkdir($saveFolder);

        $currentDir = $sourcePath ?: public_path('uploads'.$DS.'original');
        $img = $currentDir.$DS.$img;

        if (!is_file($img))
            return false;

        if (!$cropArrayName || !is_string($cropArrayName))
            return false;

        $cropArray = config('file-manager.custom_crops.'.$cropArrayName);

        if (!is_array($cropArray) || empty($cropArray))
            return false;

        $saveFolder = $saveFolder.$DS;

        $image = Image::make($img);
        $image->backup();

        // Image data
        $src = $img;
        $fileInfo = pathinfo($src);
        $imgWidth  = $image->width();
        $imgHeight = $image->height();

        // Get the image ratio
        $imageRatio = $imgWidth / $imgHeight;

        // run on each crop
        foreach ($cropArray as $cropKey => $cropData) {
            // New image name
            $newImageName = $saveFolder.$fileInfo['filename'].'_'.$cropArrayName.'_'.$cropKey.'.'.$fileInfo['extension'];

            // if the new image exists do not crop
            if (file_exists(($newImageName)))
                continue;

            // store the crop size in vars
            $orig_w = $targ_w = $cropData['width'];
            $orig_h = $targ_h = $cropData['height'];

            // if crop width bigger then the image width or the height is bigger then the image height continue to the next crop
            if (($targ_w > $imgWidth || $targ_h > $imgHeight)) {
                if (!$forceCrop) {
                    continue;
                }
            }

            // If the crop matches the exact ratio like the image + 0.03 -
            // Just resize to the crop size
            $currentCropRatio = $targ_w / $targ_h;

            if (round($imageRatio,2) < (round($currentCropRatio,2) + 0.03) && round($imageRatio,2) > (round($currentCropRatio,2) - 0.03)) {
                $image->resize($targ_w, $targ_h)->save($newImageName);
            } else {
                // Else crop to the maximum optional size and then crop to the crop size

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
                    continue;

                if(($targ_h + $targ_y) > $imgHeight)
                    continue;

                $image->crop(floor($targ_w), floor($targ_h), floor($targ_x), floor($targ_y))
                    ->resize($orig_w, $orig_h)
                    ->save($newImageName);
            }
            $image = $image->reset();
        }
    }
}