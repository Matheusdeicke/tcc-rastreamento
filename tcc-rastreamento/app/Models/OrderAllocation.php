<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAllocation extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','kit_instance_id','reserved_at','released_at'];
    protected $casts = ['reserved_at' => 'datetime'];
    public function order(){ return $this->belongsTo(Order::class); }
    public function kitInstance(){ return $this->belongsTo(KitInstance::class); }
}

