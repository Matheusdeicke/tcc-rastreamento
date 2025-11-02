<?php


namespace App\Http\Controllers\Cme;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAllocation;
use App\Models\KitInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderCmeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:cme|admin']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status');
        $q = Order::with(['requester','handler','allocations.kitInstance.kit'])->latest();
        if ($status) { $q->where('status', $status); }
        $orders = $q->paginate(15);
        return view('cme.orders.index', compact('orders','status'));
    }

    public function accept(Request $request, \App\Models\Order $order)
    {
        $data = $request->validate([
            'instance_id' => ['required','integer','exists:kit_instances,id'],
        ]);

        return DB::transaction(function () use ($order, $data) {
            $inst = KitInstance::where('id', $data['instance_id'])
                ->where('status', 'em_estoque')
                ->lockForUpdate()
                ->first();

            if (!$inst) {
                return back()->with('err', 'Instância não disponível.');
            }

            $order->update([
                'status'           => 'aceito',
                'handler_id'       => auth()->id(),
                'requested_kit_id' => $order->requested_kit_id ?: $inst->kit_id,
            ]);

            OrderAllocation::create([
                'order_id'        => $order->id,
                'kit_instance_id' => $inst->id,
                'reserved_at'     => now(),
            ]);

            return back()->with('ok', 'Pedido aceito e instância alocada.');
        });
    }

    public function reject(Request $request, Order $order)
    {
        abort_unless(in_array($order->status, ['solicitado','aceito']), 403);
        $request->validate(['observacoes' => ['required','string','max:2000']]);

        DB::transaction(function() use ($order, $request){
            $order->update([
                'status'      => 'recusado',
                'handler_id'  => auth()->id(),
                'observacoes' => trim(($order->observacoes ? $order->observacoes."\n" : '').'[CME recusou] '.$request->observacoes),
            ]);

            $alloc = $order->allocations()->latest()->lockForUpdate()->first();
            if ($alloc && !$alloc->released_at) {
                $alloc->update(['released_at'=>now()]);
            }
        });

        return back()->with('ok','Pedido recusado.');
    }

    public function startPrep(Order $order)
    {
        abort_unless(in_array($order->status, ['aceito','solicitado']), 403);

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'em_preparo']);

            $inst = optional($order->allocations()->latest()->first())->kitInstance;

            if ($inst) {
                $inst->eventos()->create([
                    'user_id'     => auth()->id(),
                    'etapa'       => 'preparo',
                    'local'       => 'CME – Arsenal',
                    'observacoes' => 'Separação e conferência do kit para preparo.',
                ]);

                $inst->update(['status' => 'em_preparo']);
            }
        });

        return back()->with('ok', 'Preparo iniciado e evento registrado.');
    }

    public function ready(Order $order)
    {
        abort_unless($order->status === 'em_preparo', 403);

        $order->update(['status'=>'pronto_para_envio']);

        $inst = optional($order->allocations()->latest()->first())->kitInstance;
        if ($inst) {
            $inst->eventos()->create([
                'user_id'     => auth()->id(),
                'etapa'       => 'envio',
                'local'       => $order->setor ?? 'Transporte',
                'observacoes' => 'Kit liberado para transporte',
            ]);
            $inst->update(['status'=>'enviado']);
        }

        return back()->with('ok','Kit pronto para envio.');
    }

    public function deliver(Order $order)
    {
        abort_unless($order->status === 'pronto_para_envio', 403);

        $order->update(['status'=>'entregue']);

        $inst = optional($order->allocations()->latest()->first())->kitInstance;
        if ($inst) {
            $inst->eventos()->create([
                'user_id'     => auth()->id(),
                'etapa'       => 'uso',
                'local'       => $order->setor ?? 'Centro Cirúrgico',
                'observacoes' => 'Recebido pelo solicitante',
            ]);
            $inst->update(['status'=>'em_uso']);
        }

        return back()->with('ok','Pedido entregue ao solicitante.');
    }

    public function close(Request $request, Order $order)
    {
        abort_unless($order->status === 'entregue', 403);

        DB::transaction(function() use ($order){
            $order->update(['status'=>'fechado']);

            $alloc = $order->allocations()->latest()->lockForUpdate()->first();
            if ($alloc && !$alloc->released_at) {
                $alloc->update(['released_at'=>now()]);
            }

            $inst = optional($alloc)->kitInstance;
            if ($inst) {
                $inst->eventos()->create([
                    'user_id'     => auth()->id(),
                    'etapa'       => 'retorno',
                    'local'       => 'CME – Recepção',
                    'observacoes' => 'Kit retornado para processamento',
                ]);
                $inst->update(['status'=>'em_lavagem']);
            }
        });

        return back()->with('ok','Pedido fechado e kit enviado para reprocesso.');
    }

    public function show(\App\Models\Order $order)
    {
        $order->load([
            'requester',
            'handler',
            'requestedKit',
            'allocations.kitInstance.kit',
            'allocations.kitInstance.eventos' => fn($q) => $q->latest(),
        ]);

        $estoque = KitInstance::with('kit')
            ->where('status', 'em_estoque')
            ->when($order->requested_kit_id, fn($q) => $q->where('kit_id', $order->requested_kit_id))
            ->orderBy('created_at')
            ->get();

        return view('cme.orders.show', compact('order','estoque'));
    }

}

