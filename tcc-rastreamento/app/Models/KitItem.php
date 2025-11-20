<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitItem extends Model
{
    protected $fillable = [
        'kit_id',
        'nome',
        'codigo',
        'quantidade',
        'observacoes',
    ];

    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }
}
