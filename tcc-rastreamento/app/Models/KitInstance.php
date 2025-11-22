<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitInstance extends Model
{
    protected $fillable = ['kit_id','etiqueta','status','data_validade'];

    protected $casts = [
        'data_validade' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
    
    public function kit()
    {
        return $this->belongsTo(\App\Models\Kit::class);
    }

    public function eventos()
    {
        return $this->hasMany(\App\Models\TraceEvent::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnRequest::class, 'kit_instance_id');
    }

    public function openReturn()
    {
        return $this->hasOne(ReturnRequest::class, 'kit_instance_id')->open();
    }
}
