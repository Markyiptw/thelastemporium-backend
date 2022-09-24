<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    use HasFactory;

    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
    ];

    protected $fillable = ['to', 'cc', 'subject', 'message', 'location'];

    public function object()
    {
        return $this->belongsTo(Obj::class, 'object_id');
    }
}
