<?php

namespace App\Http\Controllers;

use App\Http\Resources\ObjResource;
use App\Models\Obj;

class ObjController extends Controller
{
    public function index()
    {
        return ObjResource::collection(
            Obj::paginate()
        );
    }
}
