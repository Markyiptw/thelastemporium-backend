<?php

namespace App\Http\Controllers;

use App\Http\Resources\MailResource;
use App\Mail\MessageFromTheLastEmporium;
use App\Models\Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ObjectMailController extends Controller
{
    public function store(Obj $object, Request $request)
    {
        Mail::to($request->input('to'))
            ->cc($request->input('cc'))
            ->send(new MessageFromTheLastEmporium($request->input('message')));

        $mail = $object->mails()->create([
            'cc' => $request->input('cc'),
            'to' => $request->input('to'),
            'message' => $request->input('message'),
        ]);

        return new MailResource($mail);
    }
}
