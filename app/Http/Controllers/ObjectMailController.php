<?php

namespace App\Http\Controllers;

use App\Http\Resources\MailResource;
use App\Mail\MessageFromTheLastEmporium;
use App\Models\Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class ObjectMailController extends Controller
{
    public function store(Obj $object, Request $request)
    {
        Gate::authorize('object-specific-action', $object);

        $validated = $request->validate([
            'to' => ['required', 'array', 'min:1'],
            'to.*' => ['required', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['required', 'email'],
            'message' => ['required', 'string'],
        ]);

        $mail = Mail::to($validated['to']);

        if (array_key_exists('cc', $validated)) {
            $mail->cc($validated['cc']);
        }

        $mail->send(new MessageFromTheLastEmporium($validated['message']));

        $mail = $object->mails()->create([
            'to' => $validated['to'],
            'cc' => $validated['cc'] ?? null,
            'message' => $validated['message'],
        ]);

        return new MailResource($mail);
    }

    public function index(Obj $object)
    {
        Gate::authorize('object-specific-action', $object);

        return MailResource::collection(
            $object->mails()->paginate()
        );
    }
}
