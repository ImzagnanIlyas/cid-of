<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordre extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'division_id',
        'ville',
        'numero_of',
        'code_affaire',
        'observation',
        'client',
        'montant',
        'montant_devise',
        'date_envoi',
        'date_modification',
        'date_refus',
        'motif',
        'refus',
        'statut',
        'type',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function division()
    {
        return $this->belongsTo('App\Models\Division');
    }

    public function facture()
    {
        return $this->hasOne('App\Models\Facture');
    }

    public function attachements()
    {
        return $this->hasMany('App\Models\Attachement');
    }

    public function historiques()
    {
        return $this->hasMany('App\Models\Historique');
    }
}
