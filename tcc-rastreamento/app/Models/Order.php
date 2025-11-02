<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'requester_id','handler_id','status',
        'needed_at','setor','paciente','observacoes',
        'requested_kit_id',
    ];

    protected $casts = [
        'needed_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requestedKit()
    {
        return $this->belongsTo(\App\Models\Kit::class, 'requested_kit_id');
    }

    public function requester(){ 
        return $this->belongsTo(User::class, 'requester_id'); 
    }

    public function handler(){ 
        return $this->belongsTo(User::class, 'handler_id'); 
    }

    public function allocations(){ 
        return $this->hasMany(OrderAllocation::class); 
    }

        public function canBeAccepted(): bool
    {
        return filled($this->setor) && filled($this->needed_at);
    }

    public function lastRejectionReason(): ?string
    {
        if (!$this->observacoes) return null;

        $lines = preg_split("/\r\n|\r|\n/", $this->observacoes);
        $prefix = '[CME recusou]';

        $matches = array_values(array_filter($lines, function ($l) use ($prefix) {
            return str_starts_with(trim($l), $prefix);
        }));

        if (empty($matches)) return null;

        $last = trim(end($matches));
        $last = preg_replace('/^\[CME recusou\]\s*/', '', $last);
        return $last !== '' ? $last : null;
    }

}
