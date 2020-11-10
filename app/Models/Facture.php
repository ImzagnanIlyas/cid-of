<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $fillable = [
        'numero_facture',
        'montant',
        'montant_devise',
        'date_facturation',
        'date_reception_client',
        'reception_client',
        'ordre_id',
        'user_id',
    ];
    protected $appends = ['division_name', 'client'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function ordre()
    {
        return $this->belongsTo('App\Models\Ordre');
    }

    function getDivisionNameAttribute() {
        return $this->ordre->division->nom;
    }

    function getClientAttribute() {
        return $this->ordre->client;
    }
}
