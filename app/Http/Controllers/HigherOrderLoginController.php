<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
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

                return new UserResource(Auth::guard($guard)->user());
            }

            throw ValidationException::withMessages([
                'username' => 'The provided credentials do not match our records.',
            ]);
        };
    }

    public function logout($guard)
    {
        return function (Request $request) use ($guard) {
            Auth::guard($guard)->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return ['message' => "Logout successful!"];
        };
    }
}
