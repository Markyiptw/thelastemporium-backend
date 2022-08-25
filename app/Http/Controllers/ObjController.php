<?php

namespace App\Http\Controllers;

use App\Http\Resources\ObjResource;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ObjController extends Controller
{
    public function index()
    {
        return ObjResource::collection(
            Obj::paginate()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'user' => ['required', 'array'],
            'user.name' => ['required', 'string'],
            'user.email' => ['required', 'string'],
            'user.password' => ['required', 'string'],
        ]);

        $object = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['user']['name'],
                'email' => $validated['user']['email'],
                'password' => Hash::make($validated['user']['password']),
            ]);

            $object = $user->object()->create([
                'name' => $validated['name'],
            ]);

            return $object;
        });

        return new ObjResource($object->load('user'));
    }
}
