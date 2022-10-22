<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
            'created_at' => ['nullable', 'date']
        ]);

        $validated = collect($validated)->filter(fn ($value) => !is_null($value))->all();

        $location->update($validated);

        return new LocationResource($location);
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return response()->noContent();
    }
}
