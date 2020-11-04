<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'context',
        'nom',
        'ordre_id',
    ];


    public function ordre()
    {
        return $this->belongsTo('App\Models\Ordre');
    }
}
