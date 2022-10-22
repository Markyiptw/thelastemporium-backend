<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['latitude', 'longitude', 'created_at'];

    public function object()
    {
        return $this->belongsTo(Obj::class, 'object_id');
    }
}
