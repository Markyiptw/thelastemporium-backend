<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Media;

class MediaController extends Controller
{
    public function index()
    {
        return MediaResource::collection(
            Media::paginate()
        );
    }
}
