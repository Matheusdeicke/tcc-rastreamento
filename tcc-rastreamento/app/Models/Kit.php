<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    protected $fillable = ['nome','descricao'];

    public function instances()
    {
        return $this->hasMany(\App\Models\KitInstance::class);
    }

    public function items()
    {
        return $this->hasMany(KitItem::class)->orderBy('nome');
    }

    public function getTemInstanciaNaoDevolvidaAttribute(): bool
    {
        return $this->instances()
            ->whereNotIn('status', ['em_estoque'])
            ->exists();
    }
}
