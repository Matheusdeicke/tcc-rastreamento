<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnCheckItem extends Model
{
    protected $fillable = [
        'return_request_id',
        'kit_item_id',
        'expected_qty',
        'returned_qty',
        'missing_qty',
        'status',
        'observacoes',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }

    public function kitItem()
    {
        return $this->belongsTo(KitItem::class);
    }
}
