<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'kit_instance_id',
        'requested_by_user_id',
        'requested_at',
        'status',
        'notes',
        'meta',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'meta' => 'array',
    ];

    public function kitInstance(): BelongsTo
    {
        return $this->belongsTo(KitInstance::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function getStatusLabelAttribute()
    {
        return [
            'return_requested' => 'Devolução solicitada',
            'received_by_cme'  => 'Recebido pela CME',
            'quarantine'       => 'Quarentena',
            'reprocessing'     => 'Em reprocesso',
            'released'         => 'Liberado ao estoque',
        ][$this->status] ?? $this->status;
    }
}
