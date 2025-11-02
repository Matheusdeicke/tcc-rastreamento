<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Kit;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $orders = Order::with(['handler'])
            ->where('requester_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $kits = Kit::orderBy('nome')->get(['id','nome']);
        return view('orders.create', compact('kits'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'needed_at'        => ['nullable','date','after_or_equal:today'],
            'setor'            => ['nullable','string','max:120'],
            'observacoes'      => ['nullable','string','max:2000'],
            'requested_kit_id' => ['nullable','integer','exists:kits,id'],
        ]);

        $order = Order::create([
            'requester_id'     => auth()->id(),
            'status'           => 'solicitado',
            'needed_at'        => $data['needed_at'] ?? null,
            'setor'            => $data['setor'] ?? null,
            'observacoes'      => $data['observacoes'] ?? null,
            'requested_kit_id' => $data['requested_kit_id'] ?? null, 
        ]);

        return redirect()->route('orders.show', $order)->with('ok', 'Pedido criado com sucesso.');
    }

    public function show(Order $order)
    {
        abort_if($order->requester_id !== auth()->id() && !auth()->user()->hasAnyRole(['admin','cme']), 403);

        $order->load([
            'requester','handler',
            'allocations.kitInstance.kit',
            'allocations.kitInstance.eventos' => fn($q) => $q->latest(),
        ]);

        return view('orders.show', compact('order'));
    }

}

