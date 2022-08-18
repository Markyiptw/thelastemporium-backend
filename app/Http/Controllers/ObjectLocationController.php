<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Obj;

class ObjectLocationController extends Controller
{
    public function index(Obj $object)
    {
        return LocationResource::collection($object->locations()->paginate());
    }
}
