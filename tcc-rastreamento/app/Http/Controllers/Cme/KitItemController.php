<?php

namespace App\Http\Controllers\Cme;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use App\Models\KitItem;
use Illuminate\Http\Request;

class KitItemController extends Controller
{
    public function storeMany(Request $request, Kit $kit)
    {
        $data = $request->validate([
            'items'                => ['required', 'array'],
            'items.*.nome'         => ['nullable', 'string', 'max:255'],
            'items.*.codigo'       => ['nullable', 'string', 'max:255'],
            'items.*.quantidade'   => ['nullable', 'integer', 'min:1'],
            'items.*.observacoes'  => ['nullable', 'string', 'max:255'],
        ]);

        $items = collect($data['items'])->filter(function ($item) {
            return !empty($item['nome']);
        });

        if ($items->isEmpty()) {
            return back()
                ->withErrors(['items' => 'Informe pelo menos uma peça para adicionar ao kit.'])
                ->withInput();
        }

        foreach ($items as $itemData) {
            $kit->items()->create([
                'nome'        => $itemData['nome'],
                'codigo'      => $itemData['codigo'] ?? null,
                'quantidade'  => $itemData['quantidade'] ?? 1,
                'observacoes' => $itemData['observacoes'] ?? null,
            ]);
        }

        return redirect()
            ->route('kits.show', $kit)
            ->with('ok', 'Peças adicionadas ao kit com sucesso.');
    }

    public function edit(Kit $kit, KitItem $item)
    {
        abort_if($item->kit_id !== $kit->id, 404);

        return view('cme.kits.items.edit', [
            'kit'  => $kit,
            'item' => $item,
        ]);
    }

    public function update(Request $request, Kit $kit, KitItem $item)
    {
        abort_if($item->kit_id !== $kit->id, 404);

        $data = $request->validate([
            'nome'        => ['required', 'string', 'max:255'],
            'codigo'      => ['nullable', 'string', 'max:255'],
            'quantidade'  => ['required', 'integer', 'min:1'],
            'observacoes' => ['nullable', 'string', 'max:255'],
        ]);

        $item->update($data);

        return redirect()
            ->route('kits.show', $kit)
            ->with('ok', 'Peça atualizada com sucesso.');
    }

    public function destroy(Kit $kit, KitItem $item)
    {
        abort_if($item->kit_id !== $kit->id, 404);

        $item->delete();

        return redirect()
            ->route('kits.show', $kit)
            ->with('ok', 'Peça removida com sucesso.');
    }
}
