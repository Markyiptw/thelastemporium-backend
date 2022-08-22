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
        Gate::authorize('upload-media', $object);

        $fiveMbInKb = 5 * 1024;

        $request->validate([
            'file' => [
                "file",
                "max:{$fiveMbInKb}",
                "mimetypes:audio/x-m4a,image/jpeg,image/png", // https://stackoverflow.com/questions/71265563/how-to-show-spinner-while-safari-is-converting-heic-to-jpeg-via-input-type-file
                "required",
            ],
        ]);

        $file = $request->file('file');

        $path = $file->store(null, 'public');

        $media = $object->medias()->create([
            'path' => $path,
            'mime_type' => $file->getMimeType(),
        ]);

        return new MediaResource($media);
    }

    public function index(Obj $object)
    {
        return MediaResource::collection(
            $object->medias()->paginate(),
        );
    }
}
