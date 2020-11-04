<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    public function ordres()
    {
        return $this->hasMany('App\Models\Ordre');
    }

    public function pole()
    {
        return $this->belongsTo('App\Models\Pole');
    }
}
