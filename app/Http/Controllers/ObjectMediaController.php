<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ObjectMediaController extends Controller
{
    public function store(Request $request, Obj $object)
    {
        Gate::authorize('object-specific-action', $object);

        $fiveMbInKb = 5 * 1024;

        $validated = $request->validate([
            'file' => [
                "file",
                "max:{$fiveMbInKb}",
                "mimetypes:audio/x-m4a,image/jpeg,image/png,audio/mpeg", // https://stackoverflow.com/questions/71265563/how-to-show-spinner-while-safari-is-converting-heic-to-jpeg-via-input-type-file
                "required",
            ],
            'caption' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
        ]);

        $file = $request->file('file');

        $path = $file->store(null, 'public'); // relative to disk, i.e. storage/app/public/foo.jpg will just return foo.jpg

        $media = $object
            ->medias()
            ->create(
                array_merge(
                    [
                        'path' => $path,
                        'mime_type' => $file->getMimeType(),
                    ],
                    collect($validated)
                        ->except(['file'])
                        ->all()
                )
            );

        return new MediaResource($media);
    }

    public function index(Obj $object)
    {
        return MediaResource::collection(
            $object->medias()->get(),
        );
    }
}
