<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obj extends Model
{
    use HasFactory;

    protected $table = 'objects';

    public function locations()
    {
        return $this->hasMany(Location::class, 'object_id');
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'object_id');
    }
}
