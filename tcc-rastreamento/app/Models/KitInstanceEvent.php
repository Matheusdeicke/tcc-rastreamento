<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitInstanceEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'kit_instance_id', 'user_id', 'etapa', 'local', 'observacoes', 'registrado_em',
    ];

    protected $casts = [
        'registrado_em' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function kitInstance()
    {
        return $this->belongsTo(KitInstance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
