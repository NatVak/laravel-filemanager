<?php

namespace Ybaruchel\LaravelFileManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ResourcesController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function show($type, $filename)
    {
        switch ($type) {
            case 'css':
                $filePath = __DIR__ . '/../resources/css/build/' . $filename;
                if (file_exists($filePath)) {
                    return response(file_get_contents($filePath))->withHeaders(['Content-Type' => 'text/css']);
                }
                break;
            case 'js':
                $filePath = __DIR__ . '/../resources/js/' . $filename;
                if (file_exists($filePath)) {
                    return response(file_get_contents($filePath))->withHeaders(['Content-Type' => 'application/javascript']);
                }
                break;
            case 'images':
                $filePath = __DIR__ . '/../resources/images/' . $filename;
                if (file_exists($filePath)) {
                    return response(file_get_contents($filePath));
                }
                break;
        }
    }
}