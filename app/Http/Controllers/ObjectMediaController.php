<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Obj;
use Illuminate\Http\Request;

class ObjectMediaController extends Controller
{
    public function store(Request $request, Obj $object)
    {
        $fiveMbInKb = 5 * 1024;

        $request->validate([
            'file' => [
                "file",
                "max:{$fiveMbInKb}",
                "mimetypes:audio/x-m4a,image/jpeg,image/png",
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
}
