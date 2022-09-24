<?php

namespace App\Http\Controllers;

use App\Http\Resources\DraftResource;
use App\Models\Draft;
use App\Models\Obj;
use Illuminate\Http\Request;

class ObjectDraftController extends Controller
{
    public function store(Obj $object, Request $request)
    {
        $validated = $request->validate([
            'to' => ['nullable', 'array'],
            'to.*' => ['required', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['required', 'email'],
            'subject' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
            'name' => ['required', 'string'],
        ]);

        $draft = $object->drafts()->create($validated);

        return new DraftResource($draft);
    }

    public function index(Obj $object)
    {
        return DraftResource::collection(
            $object
                ->drafts()
                ->paginate()
        );
    }

    public function show(Obj $object, Draft $draft)
    {
        return new DraftResource($draft);
    }

}
