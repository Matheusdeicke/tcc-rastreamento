<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TraceEvent extends Model
{
    protected $fillable = [
        'kit_instance_id',
        'user_id',
        'etapa',
        'local',
        'observacoes',
    ];

    public function kitInstance() {
        return $this->belongsTo(KitInstance::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
