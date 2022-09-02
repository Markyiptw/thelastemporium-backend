<?php

namespace App\Http\Controllers;

use App\Http\Resources\MailResource;
use App\Mail\MessageFromTheLastEmporium;
use App\Models\Mail;
use App\Models\Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;

class ObjectMailController extends Controller
{
    public function store(Obj $object, Request $request)
    {
        $validated = $request->validate([
            'to' => ['required', 'array', 'min:1'],
            'to.*' => ['required', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['required', 'email'],
            'message' => ['required', 'string'],
        ]);

        $mail = Facades\Mail::to($validated['to']);

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
        return MailResource::collection(
            $object->mails()->paginate()
        );
    }

    public function show(Obj $object, Mail $mail)
    {
        return new MailResource($mail);
    }
}
