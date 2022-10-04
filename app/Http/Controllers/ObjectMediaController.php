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
        ]);

        $file = $request->file('file');

        $path = $file->store(null, 'public'); // relative to disk, i.e. storage/app/public/foo.jpg will just return foo.jpg

        $media = $object->medias()->create([
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'caption' => $validated['caption'],
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
