<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordre extends Model
{
    use HasFactory;

    public function division()
    {
        return $this->belongsTo('App\Division');
    }

    public function facture()
    {
        return $this->hasOne('App\Facture');
    }

    public function attachements()
    {
        return $this->hasMany('App\Attachement');
    }
}
