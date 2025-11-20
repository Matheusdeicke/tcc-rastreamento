<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'kit_item_id',
        'reported_status',
        'reported_qty',
        'notes',
        'photo_urls',
    ];

    protected $casts = [
        'photo_urls' => 'array',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}
