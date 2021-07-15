<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoRequest;
use App\Models\Photo;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::latest()->get();

        return response()->json($photos);
    }

    public function store(PhotoRequest $request)
    {
        $photo = Photo::create($request->all());

        return response()->json($photo, 201);
    }

    public function show($id)
    {
        $photo = Photo::findOrFail($id);

        return response()->json($photo);
    }

    public function update(PhotoRequest $request, $id)
    {
        $photo = Photo::findOrFail($id);
        $photo->update($request->all());

        return response()->json($photo, 200);
    }

    public function destroy($id)
    {
        Photo::destroy($id);

        return response()->json(null, 204);
    }
}