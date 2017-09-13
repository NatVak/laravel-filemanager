<?php

use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Routing\UrlGenerator;
use Ybaruchel\LaravelFileManager\Support\Links;
use Ybaruchel\LaravelFileManager\Support\Cropper;

/**
 * Function for flashing message to jquery toastr listener
 *
 * @param $data - array of data that will be passed to the toastr
 * @param $url (optional) - If set, function will redirect to this path after flashing data
 */
if (! function_exists('toastr')) {
    function toastr($data, $url = false) {
        session()->flash('toastr' ,$data);
        if($url)
            return redirect($url)->send();
    }
}

if (! function_exists('route_with_params')) {
    /**
     * Generate a url for the application and keeping the parameters of current url.
     *
     * @param string $route
     * @param array $parameters
     * @return string
     */
    function route_with_params($route, $parameters = [])
    {
        $parameters = array_merge(request()->input(), $parameters);
        return route($route, $parameters);
    }
}

if (! function_exists('file_manager_check_file')) {
    /**
     * Checks if a given extension is valid according to allowed extensions and size
     * url parameters.
     * @param $extension
     * @param $size
     * @return int
     */
    function file_manager_check_file($extension, $size)
    {
        $maxFileSize = request('maxfilesize');
        $allowedExtensions = (request('allowedExtensions') != '') ? explode(',', request('allowedExtensions')) : config('file-manager.allowed_extensions');
        if ($allowedExtensions) {
            if($maxFileSize && $size > $maxFileSize) {
                return 0;
            }
            return (in_array(strtolower($extension), $allowedExtensions)) ? 1 : 0;
        } else {
            return 0;
        }
    }
}

if (! function_exists('get_file_thumbnail')) {
    /**
     * Function for getting file thumbnail file.
     *
     * @param $path
     * @return string
     */
    function get_file_thumbnail($path)
    {
        if (File::exists(public_path('uploads/thumbnail/'.$path))) {
            return url('uploads/thumbnail/'.$path);
        }
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extensionImage = public_path('assets/admin/images/file-manager/filestypes/' . $extension . '.png');
        if (File::exists($extensionImage))
            return url('assets/admin/images/file-manager/filestypes/' . $extension . '.png');
        else
            return url('assets/admin/images/file-manager/filestypes/file.png');
    }
}

if (! function_exists('remove_extension_from_path')) {
    /**
     * Function for getting file-name without the extension.
     *
     * @param $path
     * @return string
     */
    function remove_extension_from_path($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }
}

if (! function_exists('get_extension_from_path')) {
    /**
     * Function for getting extension of file from a given path.
     *
     * @param $path
     * @return string
     */
    function get_extension_from_path($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
}

if (! function_exists('autoCrop'))
{
    /**
     * Function for cropping an image automatically according to
     * an input from custom crop config values.
     *
     * @param null $sourcePath
     * @param $img
     * @param $cropArrayName
     * @param $forceCrop
     * @return string
     */
    function autoCrop($sourcePath = NULL, $img, $cropArrayName, $forceCrop = false)
    {
        return Cropper::crop($img, $cropArrayName, $forceCrop);
    }
}

if (! function_exists('get_image_crop_url')) {
    /**
     * Function for getting image crop filename.
     *
     * @param $imagePath
     * @param $cropName
     * @param $cropSize
     * @return string
     */
    function get_image_crop_url($imagePath, $cropName, $cropSize)
    {
        return asset('uploads/crop/'.remove_extension_from_path($imagePath) . '_' . $cropName . '_' . $cropSize . '.' . get_extension_from_path($imagePath));
    }
}

if (! function_exists('get_image_crop_path')) {
    /**
     * Function for getting image crop full path.
     *
     * @param $imagePath
     * @param $cropName
     * @param $cropSize
     * @return string
     */
    function get_image_crop_path($imagePath, $cropName, $cropSize)
    {
        return public_path('uploads/crop/'.remove_extension_from_path($imagePath) . '_' . $cropName . '_' . $cropSize . '.' . get_extension_from_path($imagePath));
    }
}

if (! function_exists('get_old_inputs_as_array')) {
    /**
     * Function for getting old file manager inputs as array
     *
     * @param $inputName
     * @return string
     */
    function get_old_inputs_as_array($inputName)
    {
        $inputName = convert_input_to_dot_separated_array($inputName);
        $array = [];
        $index = 0;
        while (old($inputName.$index)) {
            $array[] = old($inputName.$index);
            $index++;
        }
        return $array;
    }
}

if (! function_exists('get_array_error_messages')) {
    /**
     * Function for getting error messages of array
     *
     * @param $errors
     * @param $inputName
     * @return string
     */
    function get_array_error_messages($errors, $inputName)
    {
        $inputName = convert_input_to_dot_separated_array($inputName);
        $errorMessages = [];
        $index = 0;

        while (old($inputName.$index)) {
            if ($errors->has($inputName.$index)) {
                $errorMessages[$index] = $errors->get($inputName.$index);
            }
            $index++;
        }

        return $errorMessages;
    }
}

/**
 * Function for converting input to dot separated array
 */
if (! function_exists('convert_input_to_dot_separated_array')) {
    function convert_input_to_dot_separated_array ($inputName) {
        $inputParts = explode('[', $inputName);
        if (count($inputParts) == 1) {
            return $inputParts[0];
        }
        $firstName = $inputParts[0];
        preg_match_all('/\[(.*?)\]/', $inputName, $matches);
        if (!isset($matches[1])) {
            return $inputName;
        }
        return $firstName . '.' . implode('.', $matches[1]);
    }
}

if (! function_exists('get_crop_min_sizes')) {
    /**
     * Function for getting the minimum crop size for a crop key
     * @param $cropName
     * @return null|string
     */
    function get_crop_min_sizes ($cropName) {
        $cropSizes = config('file-manager.custom_crops.'.$cropName);
        if (!$cropSizes || !is_array($cropSizes)) {
            return null;
        }

        $minWidth = $minHeight = null;

        foreach ($cropSizes as $cropSizeKey => $cropSizeData) {
            $minSizeWidth = array_get($cropSizeData, 'width');
            $minSizeHeight = array_get($cropSizeData, 'height');
            if (!$minWidth || $minSizeWidth > $minWidth) {
                $minWidth = $minSizeWidth;
            }
            if (!$minHeight || $minSizeHeight > $minHeight) {
                $minHeight = $minSizeHeight;
            }
        }
        return $minWidth . 'px X ' . $minHeight . 'px';
    }
}

if (! function_exists('get_image_sizes_data')) {
    /**
     * Function for getting image data json object for css crops
     * @param $imagePath
     * @param $cropName
     * @return null / country model
     */
    function get_image_sizes_data ($imagePath, $cropName) {
        $return = [];
        $cropSizes = config('file-manager.custom_crops.'.$cropName);
        if (!$cropSizes || !is_array($cropSizes)) {
            return json_encode($return);
        }

        foreach ($cropSizes as $cropSizeKey => $cropSizeData) {
            $mediaQueries = array_get($cropSizeData, 'media_query');
            if (!$mediaQueries || !is_array($mediaQueries)) {
                return json_encode($return);
            }
            $mediaQueries = http_build_query($mediaQueries,'',') and (');
            $mediaQueries = str_replace('=', '-width:', $mediaQueries);
            $return[$mediaQueries] = get_image_crop_url($imagePath, $cropName, $cropSizeKey);
        }
        return json_encode($return);
    }
}

if (! function_exists('is_file_exists')) {
    function is_file_exists ($files) {
        $response = [];
        foreach($files as $media => $file) {
            if(file_exists(public_path(str_replace(url(''), '', $file))))
                $response[$media] = $file;
        }

        return $response;
    }
}

if (! function_exists('get_alt_by_path')) {
    /**
     * Function for getting image crop alt filename.
     * @param $path = image source path
     * @return string
     */
    function get_alt_by_path($path)
    {
        return Links::getImageAlt($path);
    }
}