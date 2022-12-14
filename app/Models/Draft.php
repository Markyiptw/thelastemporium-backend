<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    use HasFactory;

    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
    ];

    protected $fillable = ['from', 'to', 'cc', 'message', 'location'];

    public function object()
    {
        return $this->belongsTo(Obj::class, 'object_id');
    }

}
