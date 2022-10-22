<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ObjectLocationController extends Controller
{
    public function index(Obj $object)
    {
        return LocationResource::collection($object->locations()->get());
    }

    public function store(Request $request, Obj $object)
    {
        Gate::authorize('object-specific-action', $object);

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
        ]);

        $location = $object->locations()->create($validated);

        return new LocationResource($location);
    }
}
