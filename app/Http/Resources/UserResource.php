<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->when($request->input('user.password'), $request->input('user.password')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
