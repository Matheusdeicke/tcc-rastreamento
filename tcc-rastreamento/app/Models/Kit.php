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

    public function getTemInstanciaNaoDevolvidaAttribute(): bool
    {
        if (array_key_exists('tem_instancia_nao_devolvida', $this->attributes)) {
            return (bool) $this->attributes['tem_instancia_nao_devolvida'];
        }

        return $this->instances()
            ->where('status', '!=', 'devolvido')
            ->exists();
    }
}




