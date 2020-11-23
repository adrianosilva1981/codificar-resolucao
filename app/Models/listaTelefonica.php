<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class listaTelefonica extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'alias',
        'party',
        'address',
        'phone',
        'fax',
        'email',
        'born_city',
        'born_state',
        'birth',
        'social_medias'
    ];

}
