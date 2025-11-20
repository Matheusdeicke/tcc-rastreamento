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
            'items' => ['required', 'array'],
            'items.*.nome' => ['required', 'string', 'max:255'],
            'items.*.codigo' => ['nullable', 'string', 'max:255'],
            'items.*.quantidade' => ['required', 'integer', 'min:1'],
            'items.*.observacoes' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data['items'] as $itemData) {
            // ignora linhas totalmente vazias
            if (empty($itemData['nome'])) {
                continue;
            }

            $kit->items()->create($itemData);
        }

        return redirect()
            ->route('kits.show', $kit)
            ->with('ok', 'Peças adicionadas ao kit com sucesso.');
    }

    public function edit(Kit $kit, KitItem $item)
    {
        // garantir que a peça pertence ao kit
        abort_if($item->kit_id !== $kit->id, 404);

        return view('cme.kits.items.edit', [
            'kit' => $kit,
            'item' => $item,
        ]);
    }

    public function update(Request $request, Kit $kit, KitItem $item)
    {
        abort_if($item->kit_id !== $kit->id, 404);

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:1'],
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
