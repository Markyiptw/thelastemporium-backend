<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    protected $fillable = [
        'path',
        'mime_type',
        'caption',
        'created_at'
        'latitude',
        'longitude',
    ];

    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::disk('public')->url($value),
        );
    }

    public function object()
    {
        return $this->belongsTo(Obj::class, 'object_id');
    }
}
