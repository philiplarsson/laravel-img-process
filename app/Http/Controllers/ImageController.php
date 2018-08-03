<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        dd($file->getSize());
    }
}
