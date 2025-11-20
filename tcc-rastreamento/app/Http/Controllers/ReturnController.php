<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use App\Models\ReturnItem;
use App\Models\KitInstance;
use App\Models\TraceEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = ReturnRequest::with(['kitInstance.kit'])
            ->where('requested_by_user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        return view('returns.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'etiqueta' => ['required','string','max:120'],
            'notes'    => ['nullable','string','max:2000'],
            'items'    => ['nullable','array'],
            'items.*.kit_item_id'     => ['nullable','integer'],
            'items.*.reported_status' => ['required_with:items','in:ok,missing,damaged,contaminated'],
            'items.*.reported_qty'    => ['nullable','integer','min:0'],
            'items.*.notes'           => ['nullable','string','max:2000'],
            'items.*.photo_urls'      => ['nullable','array'],
        ]);

        return DB::transaction(function () use ($request, $data) {
            $inst = KitInstance::where('etiqueta', $data['etiqueta'])->lockForUpdate()->first();
            abort_unless($inst, 404, 'Instância não encontrada pela etiqueta.');

            $ret = ReturnRequest::create([
                'kit_instance_id'      => $inst->id,
                'requested_by_user_id' => $request->user()->id,
                'requested_at'         => now(),
                'status'               => 'return_requested',
                'notes'                => $data['notes'] ?? null,
                'meta'                 => ['source' => 'web'],
            ]);

            foreach (($data['items'] ?? []) as $it) {
                ReturnItem::create([
                    'return_id'       => $ret->id,
                    'kit_item_id'     => $it['kit_item_id'] ?? null,
                    'reported_status' => $it['reported_status'],
                    'reported_qty'    => $it['reported_qty'] ?? 1,
                    'notes'           => $it['notes'] ?? null,
                    'photo_urls'      => $it['photo_urls'] ?? [],
                ]);
            }

            TraceEvent::create([
                'kit_instance_id' => $inst->id,
                'user_id'         => $request->user()->id,
                'etapa'           => 'retorno_solicitado',
                'local'           => 'Setor/Unidade',
                'observacoes'     => 'Devolução solicitada pelo solicitante',
            ]);

            if (in_array($inst->status, ['em_uso','enviado','pronto_para_envio'])) {
                $inst->update(['status' => 'retornado']);
            }

            return redirect()->route('returns.show', $ret)->with('ok','Devolução registrada. Aguarde confirmação da CME.');
        });
    }

    public function show(ReturnRequest $returnRequest)
    {
        abort_if(
            $returnRequest->requested_by_user_id !== auth()->id()
            && !auth()->user()->hasAnyRole(['admin','cme']),
            403
        );

        $returnRequest->load(['kitInstance.kit','requester','items']);

        return view('returns.show', compact('returnRequest'));
    }
}
