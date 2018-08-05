<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use App\Jobs\ProcessImage;

class ImageController extends Controller
{
    public function index()
    {
        return view('photos');
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|file|max:19000'
        ]);

        $file = $request->file('photo');

        $filePath = $file->store('unprocessed_images');

        $image = new Image;
        $image->unprocessed_path = $filePath;
        $image->filename = basename($filePath);
        $image->save();

        ProcessImage::dispatch($image);

        return view('welcome');
    }
}
