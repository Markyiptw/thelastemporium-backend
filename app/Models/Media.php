<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    protected $fillable = [
        'path',
        'mime_type',
    ];

    public function object()
    {
        return $this->belongsTo(Obj::class, 'object_id');
    }
}
