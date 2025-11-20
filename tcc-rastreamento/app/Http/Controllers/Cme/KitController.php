<?php

namespace App\Http\Controllers\Cme;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use App\Models\KitInstance;
use App\Models\OrderAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KitController extends Controller
{
    public function index() {
        $kits = Kit::query()
            ->withExists(['instances as tem_instancia_nao_devolvida' => function ($q) {
                $q->where('status', '!=', 'devolvido');
            }])
            ->latest()
            ->paginate(12);

        return view('cme.kits.index', compact('kits'));
    }

    public function create() {
        return view('cme.kits.create');
    }

    public function store(Request $r) {
        $data = $r->validate([
            'nome' => 'required|string|max:120|unique:kits,nome',
            'descricao' => 'nullable|string|max:2000',
        ]);
        $kit = Kit::create($data);
        return redirect()->route('kits.show', $kit)->with('ok','Kit criado.');
    }

    // public function show(Kit $kit) {
    //     $kit->load(['instances' => fn($q) => $q->orderByDesc('created_at')]);
    //     return view('cme.kits.show', compact('kit'));
    // }

    public function show(Kit $kit)
    {
        $kit->load(['instances', 'instances.eventos.user', 'items']);

        return view('cme.kits.show', compact('kit'));
    }


    public function edit(Kit $kit) {
        return view('cme.kits.edit', compact('kit'));
    }

    public function update(Request $r, Kit $kit) {
        $data = $r->validate([
            'nome' => 'required|string|max:120|unique:kits,nome,'.$kit->id,
            'descricao' => 'nullable|string|max:2000',
        ]);
        $kit->update($data);
        return redirect()->route('kits.show',$kit)->with('ok','Kit atualizado.');
    }

    public function destroy(Kit $kit)
    {
        $kit->load('instances:id,kit_id,status');

        $temNaoDevolvida = KitInstance::where('kit_id', $kit->id)
            ->where('status', '!=', 'devolvido')
            ->exists();

        if ($temNaoDevolvida) {
            return back()->with('erro', 'Este kit possui instâncias não devolvidas. Devolva todas antes de excluir.');
        }

        $instanciaIds = $kit->instances->pluck('id');
        $temAlocacaoAtiva = OrderAllocation::whereIn('kit_instance_id', $instanciaIds)
            ->whereNull('deallocated_at')
            ->exists();

        if ($temAlocacaoAtiva) {
            return back()->with('erro', 'Há alocações ativas para instâncias deste kit. Finalize-as antes de excluir.');
        }

        DB::transaction(function () use ($kit) {
            $kit->instances()->delete();
            $kit->delete();
        });

        return redirect()->route('kits.index')->with('ok', 'Kit excluído com sucesso.');
    }
}
