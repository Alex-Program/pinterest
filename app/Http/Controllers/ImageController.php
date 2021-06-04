<?php


namespace App\Http\Controllers;


use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    const USER_PHOTOS = 'public/images/users';

    static public function loadImage($file, $path): string {

        $name = Storage::put($path, $file);
        return basename($name);
    }

}
