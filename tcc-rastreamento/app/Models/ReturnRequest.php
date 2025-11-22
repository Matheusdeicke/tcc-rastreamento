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

    public const STATUS_RETURN_REQUESTED = 'return_requested';
    public const STATUS_RECEIVED_BY_CME  = 'received_by_cme';
    public const STATUS_QUARANTINE       = 'quarantine';
    public const STATUS_REPROCESSING     = 'reprocessing';
    public const STATUS_RELEASED         = 'released';

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [
            self::STATUS_RETURN_REQUESTED,
            self::STATUS_RECEIVED_BY_CME,
            self::STATUS_QUARANTINE,
            self::STATUS_REPROCESSING,
        ]);
    }

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

    public function checkItems()
    {
        return $this->hasMany(ReturnCheckItem::class, 'return_request_id');
    }
}
