<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    public function ordres()
    {
        return $this->hasMany('App\Ordre');
    }

    public function pole()
    {
        return $this->belongsTo('App\Pole');
    }
}
