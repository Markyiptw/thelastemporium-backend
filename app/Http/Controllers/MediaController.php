<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index()
    {
        return MediaResource::collection(
            Media::get()
        );
    }

    public function update(Media $media, Request $request)
    {
        $validated = $request->validate([
            'caption' => ['required', 'string'],
            'created_at' => ['nullable', 'date'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
        ]);

        $validated = collect($validated)->filter(fn ($value) => !is_null($value))->all();

        $media->update($validated);

        return new MediaResource($media);
    }


    public function destroy(Media $media)
    {
        $media->delete();
        return response()->noContent();
    }
}
