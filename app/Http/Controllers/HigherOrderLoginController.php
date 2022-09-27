<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HigherOrderLoginController extends Controller
{
    public function authenticate($guard)
    {
        return function (Request $request) use ($guard) {
            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();

                return response('', 200);
            }

            throw ValidationException::withMessages([
                'username' => 'The provided credentials do not match our records.',
            ]);
        };
    }
}
