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
    public function index()
    {
        $kits = Kit::query()
            ->withExists(['instances as tem_instancia_nao_devolvida' => function ($q) {
                $q->whereIn('status', [
                    'em_uso',
                    'enviado',
                    'em_preparo',
                    'em_lavagem',
                    'quarentena',
                    'contaminado',
                ]);
            }])
            ->latest()
            ->paginate(12);

        return view('cme.kits.index', compact('kits'));
    }

    public function create()
    {
        return view('cme.kits.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nome'      => 'required|string|max:120|unique:kits,nome',
            'descricao' => 'nullable|string|max:2000',
        ]);

        $kit = Kit::create($data);

        return redirect()
            ->route('kits.show', $kit)
            ->with('ok', 'Kit criado.');
    }

    public function show(Kit $kit)
    {
        $kit->load(['instances', 'instances.eventos.user', 'items']);

        return view('cme.kits.show', compact('kit'));
    }

    public function edit(Kit $kit)
    {
        return view('cme.kits.edit', compact('kit'));
    }

    public function update(Request $r, Kit $kit)
    {
        $data = $r->validate([
            'nome'      => 'required|string|max:120|unique:kits,nome,' . $kit->id,
            'descricao' => 'nullable|string|max:2000',
        ]);

        $kit->update($data);

        return redirect()
            ->route('kits.show', $kit)
            ->with('ok', 'Kit atualizado.');
    }

    public function destroy(Kit $kit)
    {
        $kit->load('instances:id,kit_id,status');

        $statusBloqueio = [
            'em_uso',
            'enviado',
            'em_preparo',
            'em_lavagem',
            'quarentena',
            'contaminado',
        ];

        $temNaoDevolvida = $kit->instances()
            ->whereIn('status', $statusBloqueio)
            ->exists();

        if ($temNaoDevolvida) {
            return back()->with(
                'erro',
                'Este kit possui instâncias não devolvidas (em uso, envio ou reprocesso). Devolva/conclua todas antes de excluir.'
            );
        }

        $instanciaIds = $kit->instances->pluck('id');

        if ($instanciaIds->isNotEmpty()) {
            $temAlocacaoAtiva = OrderAllocation::whereIn('kit_instance_id', $instanciaIds)
                ->whereNull('released_at') // 👈 aqui é released_at
                ->exists();

            if ($temAlocacaoAtiva) {
                return back()->with(
                    'erro',
                    'Há alocações ativas para instâncias deste kit. Finalize-as antes de excluir.'
                );
            }
        }

        DB::transaction(function () use ($kit) {
            $kit->instances()->delete();
            $kit->delete();
        });

        return redirect()
            ->route('kits.index')
            ->with('ok', 'Kit excluído com sucesso.');
    }

    public function storeInstances(Request $request, Kit $kit)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ], [
            'quantity.required' => 'Informe a quantidade de instâncias.',
            'quantity.integer'  => 'A quantidade deve ser um número inteiro.',
            'quantity.min'      => 'Informe pelo menos 1 instância.',
            'quantity.max'      => 'Por segurança, crie no máximo 50 de uma vez.',
        ]);

        $existingCount = $kit->instances()->count();

        DB::transaction(function () use ($kit, $data, $existingCount) {
            for ($i = 1; $i <= $data['quantity']; $i++) {
                $seq = $existingCount + $i;

                $kit->instances()->create([
                    'etiqueta' => $kit->nome . ' #' . str_pad($seq, 2, '0', STR_PAD_LEFT),
                    'status'   => 'em_estoque',
                ]);
            }
        });

        return back()->with('ok', $data['quantity'] . ' instância(s) criada(s) com sucesso.');
    }
}
