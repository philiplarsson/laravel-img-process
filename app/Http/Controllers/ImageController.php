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
            'photos' => 'required|array',
            'photos.*' => 'required|image|file|max:19000'
        ]);

        $files = $request->file('photos');

        foreach ($files as $file) {
            $filePath = $file->store('unprocessed_images');

            $image = new Image;
            $image->unprocessed_path = $filePath;
            $image->filename = basename($filePath);
            $image->save();

            ProcessImage::dispatch($image);
        }

        return redirect()
            ->route('home')
            ->with('status', 'Image is being processed, but manual refresh when done is needed. ');
    }
}
