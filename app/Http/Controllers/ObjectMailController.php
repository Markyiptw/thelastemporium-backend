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
            'from' => ['required', 'string'],
            'to' => ['required', 'array', 'min:1'],
            'to.*' => ['required', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['required', 'email'],
            'message' => ['required', 'string'],
            'location' => ['required', 'string'],
        ]);

        $timestamp = now();

        $mail = Facades\Mail::to($validated['to']);

        if (array_key_exists('cc', $validated)) {
            $mail->cc($validated['cc']);
        }

        $mail->send(new MessageFromTheLastEmporium($validated['message'], $validated['from'], $validated['location'], $timestamp));

        $mail = $object
            ->mails()
            ->create(array_merge(
                $validated,
                [
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]
            ));

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
