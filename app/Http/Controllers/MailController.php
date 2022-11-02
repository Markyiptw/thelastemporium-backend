<?php

namespace App\Http\Controllers;

use App\Http\Resources\MailResource;
use App\Models\Mail;

class MailController extends Controller
{
    public function index()
    {
        return MailResource::collection(
            Mail::latest()->get()
        );
    }

    public function show(Mail $mail)
    {
        return new MailResource($mail);
    }

    public function destroy(Mail $mail)
    {
        $mail->delete();
        return response()->noContent();
    }
}
