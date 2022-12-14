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
            Obj::get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'user' => ['required', 'array:username,password'],
            'user.username' => ['required', 'string'],
            'user.password' => ['required', 'string'],
        ]);

        $object = DB::transaction(function () use ($validated) {
            $user = User::create([
                'username' => $validated['user']['username'],
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
