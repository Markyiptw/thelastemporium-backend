<?php

namespace App\Http\Controllers;

use App\Http\Resources\DraftResource;
use App\Models\Draft;
use App\Models\Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ObjectDraftController extends Controller
{

    public function store(Obj $object, Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'string'],
            'to' => ['nullable', 'array'],
            'to.*' => ['required', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['required', 'email'],
            'message' => ['required', 'string'],
            'location' => ['required', 'string'],
        ]);

        $draft = $object->drafts()->create($validated);

        return new DraftResource($draft);
    }

    public function index(Obj $object)
    {
        return DraftResource::collection(
            $object
                ->drafts()
                ->get()
        );
    }

    public function show(Obj $object, Draft $draft)
    {
        return new DraftResource($draft);
    }

    public function update(Obj $object, Draft $draft, Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'string'],
            'to' => ['nullable', 'array'],
            'to.*' => ['required', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['required', 'email'],
            'message' => ['required', 'string'],
            'location' => ['required', 'string'],
        ]);

        $draft->update($validated);

        return new DraftResource($draft);
    }

    public function send(Obj $object, Draft $draft, Request $request)
    {
        $mailResource = App::make(ObjectMailController::class)->store($object, $request);
        $draft->delete();
        return $mailResource;
    }

    public function destroy(Obj $object, Draft $draft)
    {
        $draft->delete();
        return response()->noContent();
    }
}
