<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'user_id',
        'ordre_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function ordre()
    {
        return $this->belongsTo('App\Models\Ordre');
    }
}
