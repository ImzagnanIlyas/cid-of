<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orj extends Ordre
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
}
