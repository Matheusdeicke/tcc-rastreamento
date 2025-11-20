<?php

namespace App\Http\Controllers\Cme;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\KitInstance;
use App\Models\TraceEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnCmeController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $q = ReturnRequest::with(['kitInstance.kit','requester'])->latest();
        if ($status) { $q->where('status',$status); }
        $returns = $q->paginate(15);

        return view('cme.returns.index', compact('returns','status'));
    }

    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load(['kitInstance.kit','requester','items']);
        return view('cme.returns.show', compact('returnRequest'));
    }

    public function confirmReceipt(ReturnRequest $returnRequest)
    {
        abort_unless($returnRequest->status === 'return_requested', 403);

        return DB::transaction(function () use ($returnRequest) {
            $inst = $returnRequest->kitInstance()->lockForUpdate()->first();

            $returnRequest->update(['status' => 'received_by_cme']);

            TraceEvent::create([
                'kit_instance_id' => $inst->id,
                'user_id'         => auth()->id(),
                'etapa'           => 'recebido_cme',
                'local'           => 'CME – Recepção',
                'observacoes'     => 'CME confirmou recebimento físico',
            ]);

            $inst->update(['status' => 'em_lavagem']);

            return back()->with('ok','Recebimento confirmado.');
        });
    }

    public function sendToQuarantine(ReturnRequest $returnRequest)
    {
        abort_unless(in_array($returnRequest->status, ['received_by_cme','return_requested']), 403);

        return DB::transaction(function () use ($returnRequest) {
            $inst = $returnRequest->kitInstance()->lockForUpdate()->first();

            $returnRequest->update(['status' => 'quarantine']);

            TraceEvent::create([
                'kit_instance_id' => $inst->id,
                'user_id'         => auth()->id(),
                'etapa'           => 'quarentena',
                'local'           => 'CME – Quarentena',
                'observacoes'     => 'Encaminhado para inspeção/quarentena',
            ]);

            $inst->update(['status' => 'quarentena']);

            return back()->with('ok','Encaminhado para quarentena.');
        });
    }

    public function sendToReprocess(ReturnRequest $returnRequest)
    {
        abort_unless(in_array($returnRequest->status, ['received_by_cme','quarantine']), 403);

        return DB::transaction(function () use ($returnRequest) {
            $inst = $returnRequest->kitInstance()->lockForUpdate()->first();

            $returnRequest->update(['status' => 'reprocessing']);

            TraceEvent::create([
                'kit_instance_id' => $inst->id,
                'user_id'         => auth()->id(),
                'etapa'           => 'reprocesso',
                'local'           => 'CME – Lavagem/Preparação',
                'observacoes'     => 'Encaminhado para reprocesso',
            ]);

            $inst->update(['status' => 'em_lavagem']);

            return back()->with('ok','Reprocessamento iniciado.');
        });
    }

    public function releaseToStock(ReturnRequest $returnRequest)
    {
        abort_unless(in_array($returnRequest->status, ['reprocessing','quarantine','received_by_cme']), 403);

        return DB::transaction(function () use ($returnRequest) {
            $inst = $returnRequest->kitInstance()->lockForUpdate()->first();

            $returnRequest->update(['status' => 'released']);

            TraceEvent::create([
                'kit_instance_id' => $inst->id,
                'user_id'         => auth()->id(),
                'etapa'           => 'liberado',
                'local'           => 'CME – Arsenal',
                'observacoes'     => 'Devolução concluída; kit liberado ao estoque',
            ]);

            $inst->update(['status' => 'em_estoque']);

            return back()->with('ok','Kit liberado para estoque.');
        });
    }
}
