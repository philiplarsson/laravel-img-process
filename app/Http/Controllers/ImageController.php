<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use App\Jobs\ProcessImage;
use Validator;

class ImageController extends Controller
{
    public function index()
    {
        return view('photos');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|file|max:19000'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => $validator->errors()->first('file')
                ], 422);
            } else {
                return redirect('/images')
                    ->withErrors($validator);
            }
        }

        $file = $request->file('file');

        $filePath = $file->store('unprocessed_images');

        $image = new Image;
        $image->unprocessed_path = $filePath;
        $image->filename = basename($filePath);
        $image->save();

        ProcessImage::dispatch($image);

        return;
    }
}
