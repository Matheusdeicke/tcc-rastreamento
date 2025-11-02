@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">
    <div class="flex items-baseline justify-between mb-4">
        <div>
            <h3 class="text-2xl font-bold text-brand-800">Pedidos de Kits — CME</h3>
            <p class="text-sm text-brand-700/80">Gestão e rastreabilidade de solicitações</p>
        </div>
    </div>

    @if(session('ok'))
        <div class="mb-3 rounded-lg bg-green-50 text-green-700 px-4 py-2 border border-green-200">{{ session('ok') }}</div>
    @elseif(session('err'))
        <div class="mb-3 rounded-lg bg-red-50 text-red-700 px-4 py-2 border border-red-200">{{ session('err') }}</div>
    @endif

    <div class="mb-4 bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-3">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <label for="status" class="text-sm text-brand-800 font-medium">Status:</label>
            <select id="status" name="status"
                    class="rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm">
                <option value="">Todos os status</option>
                @foreach(['solicitado','aceito','em_preparo','pronto_para_envio','entregue','fechado','recusado'] as $st)
                    <option value="{{ $st }}" @selected($status === $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                @endforeach
            </select>

            <button class="inline-flex items-center rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white
                           px-3 py-2 text-sm font-semibold transition">
                Filtrar
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm font-semibold text-brand-800">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Solicitante</th>
                        <th class="px-4 py-3">Setor</th>
                        <th class="px-4 py-3">Material</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($orders as $order)
                        @php
                            $badge = match($order->status){
                                'solicitado' => 'bg-blue-50 text-blue-800',
                                'aceito' => 'bg-yellow-50 text-yellow-800',
                                'em_preparo' => 'bg-sky-50 text-sky-800',
                                'pronto_para_envio' => 'bg-indigo-50 text-indigo-800',
                                'entregue' => 'bg-emerald-50 text-emerald-800',
                                'fechado' => 'bg-gray-100 text-gray-700',
                                'recusado' => 'bg-rose-50 text-rose-800',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/60" x-data>
                            <td class="px-4 py-3 font-semibold text-gray-600">#{{ $order->id }}</td>
                            <td class="px-4 py-3">{{ $order->requester->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $order->setor ?? '—' }}</td>
                            <td class="px-4 py-3">
                            {{ $order->requestedKit?->nome ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ ucfirst(str_replace('_',' ',$order->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('cme.orders.show', $order) }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white text-xs font-semibold transition">
                                        Detalhes
                                    </a>

                                    {{-- Rejeitar (modal na linha) --}}
                                    @if(in_array($order->status, ['solicitado','aceito']))
                                        <button type="button"
                                                x-on:click="$refs['reject-{{ $order->id }}'].showModal()"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg border border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white text-xs font-semibold transition">
                                            Rejeitar
                                        </button>

                                        <dialog x-ref="reject-{{ $order->id }}" class="rounded-xl p-0 w-full max-w-lg backdrop:bg-black/30">
                                            <form method="POST" action="{{ route('cme.orders.reject', $order) }}" class="bg-white rounded-xl p-5">
                                                @csrf
                                                <h4 class="text-lg font-semibold mb-2">Motivo da rejeição</h4>
                                                <p class="text-sm text-gray-600 mb-3">Esta mensagem aparecerá para o solicitante.</p>
                                                <textarea name="observacoes" rows="4" required
                                                          class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-rose-600"></textarea>
                                                <div class="mt-4 flex justify-end gap-2">
                                                    <button type="button"
                                                            x-on:click="$refs['reject-{{ $order->id }}'].close()"
                                                            class="px-3 py-1.5 rounded-md border border-gray-300 text-xs">
                                                        Cancelar
                                                    </button>
                                                    <button class="px-3 py-1.5 rounded-md bg-rose-600 text-white text-xs font-semibold">
                                                        Confirmar rejeição
                                                    </button>
                                                </div>
                                            </form>
                                        </dialog>
                                    @endif

                                    {{-- Aceitar -> vai para detalhes (selecionar instância) --}}
                                    @if($order->status === 'solicitado')
                                        <a href="{{ route('cme.orders.show', $order) }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                                            Aceitar
                                        </a>
                                    @endif

                                    @if($order->status === 'aceito')
                                        <form method="POST" action="{{ route('cme.orders.prep', $order) }}">
                                            @csrf
                                            <button class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition">
                                                Preparar
                                            </button>
                                        </form>
                                    @endif

                                    @if($order->status === 'em_preparo')
                                        <form method="POST" action="{{ route('cme.orders.ready', $order) }}">
                                            @csrf
                                            <button class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold transition">
                                                Pronto p/ envio
                                            </button>
                                        </form>
                                    @endif

                                    @if($order->status === 'pronto_para_envio')
                                        <form method="POST" action="{{ route('cme.orders.deliver', $order) }}">
                                            @csrf
                                            <button class="inline-flex items-center px-3 py-1.5 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-xs font-semibold transition">
                                                Entregar
                                            </button>
                                        </form>
                                    @endif

                                    @if($order->status === 'entregue')
                                        <form method="POST" action="{{ route('cme.orders.close', $order) }}">
                                            @csrf
                                            <button class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold transition">
                                                Fechar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhum pedido encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
