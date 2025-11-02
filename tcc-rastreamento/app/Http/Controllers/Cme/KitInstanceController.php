<?php
namespace App\Http\Controllers\Cme;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use App\Models\KitInstance;
use Illuminate\Http\Request;

class KitInstanceController extends Controller
{
    private array $status = [
        'em_lavagem','em_montagem','embalado','esterilizado','em_estoque',
        'enviado','em_uso','retornado','quarentena','descartado'
    ];

    public function create(Kit $kit) {
        $status = $this->status;
        return view('cme.kits.instances.create', compact('kit','status'));
    }

    public function store(Request $r, Kit $kit) {
        $data = $r->validate([
            'etiqueta' => 'required|string|max:120|unique:kit_instances,etiqueta',
            'status' => 'required|string|in:em_lavagem,em_montagem,embalado,esterilizado,em_estoque,enviado,em_uso,retornado,quarentena,descartado',
            'data_validade' => 'nullable|date|after:now',
        ]);
        $kit->instances()->create($data);
        return redirect()->route('kits.show',$kit)->with('ok','Instância criada.');
    }

    public function edit(KitInstance $instance) {
        $status = $this->status;
        $kit = $instance->kit;
        return view('cme.kits.instances.edit', compact('instance','status','kit'));
    }

    public function update(Request $r, KitInstance $instance) {
        $data = $r->validate([
            'etiqueta' => 'required|string|max:120|unique:kit_instances,etiqueta,'.$instance->id,
            'status' => 'required|string|in:em_lavagem,em_montagem,embalado,esterilizado,em_estoque,enviado,em_uso,retornado,quarentena,descartado',
            'data_validade' => 'nullable|date|after:now',
        ]);
        $instance->update($data);
        return redirect()->route('kits.show',$instance->kit)->with('ok','Instância atualizada.');
    }

    public function destroy(KitInstance $instance) {
        $kit = $instance->kit;
        $instance->delete();
        return redirect()->route('kits.show',$kit)->with('ok','Instância removida.');
    }
}
