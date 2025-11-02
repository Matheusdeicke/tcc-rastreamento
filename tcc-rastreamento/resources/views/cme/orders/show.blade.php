@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-4 sm:p-6">
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
        $inst = $order->allocations->last()->kitInstance ?? null;
        $eventos = $inst?->eventos ?? collect();
    @endphp

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <h3 class="text-2xl font-bold text-brand-800">Pedido #{{ $order->id }}</h3>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                {{ ucfirst(str_replace('_',' ',$order->status)) }}
            </span>
        </div>
        <a href="{{ route('cme.orders') }}"
           class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
            Voltar
        </a>
    </div>

    @if(session('ok'))
        <div class="mb-3 rounded-lg bg-green-50 text-green-700 px-4 py-2 border border-green-200">{{ session('ok') }}</div>
    @elseif(session('err'))
        <div class="mb-3 rounded-lg bg-red-50 text-red-700 px-4 py-2 border border-red-200">{{ session('err') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-3 rounded-lg bg-rose-50 text-rose-700 px-4 py-2 border border-rose-200">
            <ul class="list-disc pl-6">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-4">
            <h5 class="text-lg font-semibold text-brand-800 mb-3">Dados da Solicitação</h5>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                <div>
                    <dt class="font-medium text-gray-600">Solicitante</dt>
                    <dd>{{ $order->requester->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Setor</dt>
                    <dd>{{ $order->setor ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="font-medium text-gray-600">Observações</dt>
                    <dd class="whitespace-pre-line">{{ $order->observacoes ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Data solicitada</dt>
                    <dd>{{ optional($order->needed_at)?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Solicitado em</dt>
                    <dd>{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>

            @if($order->requestedKit)
                <div class="mt-3 text-sm text-gray-700">
                    <span class="font-medium">Material solicitado:</span>
                    {{ $order->requestedKit->nome }}
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-4">
            <h5 class="text-lg font-semibold text-brand-800 mb-3">Kit Alocado</h5>
            @if($order->allocations->isNotEmpty())
                @php $alloc = $order->allocations->last(); @endphp
                <div class="text-sm space-y-1">
                    <p><span class="font-medium text-gray-600">Kit:</span> {{ $alloc->kitInstance->kit->nome }}</p>
                    <p><span class="font-medium text-gray-600">Etiqueta:</span> {{ $alloc->kitInstance->etiqueta }}</p>
                    <p><span class="font-medium text-gray-600">Status:</span> {{ $alloc->kitInstance->status }}</p>
                    <p><span class="font-medium text-gray-600">Validade:</span> {{ optional($alloc->kitInstance->data_validade)?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
            @else
                <p class="text-sm text-gray-500">Nenhum kit alocado ainda.</p>
            @endif
        </div>
    </div>

    @if($inst)
        <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-4 mt-4">
            <h5 class="text-lg font-semibold text-brand-800 mb-3">Rastreabilidade do Kit ({{ $inst->etiqueta }})</h5>
            <div class="relative pl-6">
                <div class="absolute left-2 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                <div class="space-y-3">
                    @forelse($eventos as $ev)
                        <div class="relative">
                            <div class="absolute -left-0.5 top-1 w-3 h-3 rounded-full bg-brand-accent ring-4 ring-brand-accent/20"></div>
                            <div class="ml-2">
                                <div class="text-sm font-semibold text-gray-800">{{ ucfirst($ev->etapa) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $ev->local ?? '—' }} • {{ $ev->created_at->format('d/m/Y H:i') }}
                                </div>
                                @if($ev->observacoes)
                                    <div class="text-xs text-gray-600 mt-1">{{ $ev->observacoes }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Nenhum evento registrado ainda.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- Aceitar: exige seleção de instância --}}
    @if($order->status === 'solicitado')
        <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-4 mt-4">
            <h5 class="text-lg font-semibold text-brand-800 mb-3">Aceitar pedido</h5>

            @if($estoque->isEmpty())
                <div class="rounded-md border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3">
                    ⚠️ Não há material em estoque compatível com este pedido.
                    @if($order->requestedKit)
                        <div class="mt-1 text-sm">
                            Pedido solicita: <strong>{{ $order->requestedKit->nome }}</strong>.<br>
                            Cadastre/prepare ao menos 1 instância deste kit e retorne para aceitar.
                        </div>
                    @else
                        <div class="mt-1 text-sm">
                            O solicitante não especificou material. Cadastre/prepare qualquer instância em
                            <em>Estoque</em> e selecione-a para aceitar.
                        </div>
                    @endif
                </div>
            @else
                <form method="POST" action="{{ route('cme.orders.accept', $order) }}" class="grid gap-3 md:grid-cols-[1fr_auto]">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selecionar material (instância)</label>
                        <select name="instance_id" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm">
                            <option value="">— Selecione uma instância —</option>
                            @foreach($estoque as $instItem)
                                <option value="{{ $instItem->id }}">
                                    #{{ $instItem->id }} — {{ $instItem->kit->nome }}
                                    — Etiqueta: {{ $instItem->etiqueta }}
                                    — Validade: {{ optional($instItem->data_validade)->format('d/m/Y') ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                        @error('instance_id')
                            <p class="text-rose-700 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="self-end">
                        <button class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                            Aceitar e Alocar
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    {{-- Ações CME --}}
    <div class="mt-4 flex flex-wrap gap-2" x-data>
        @if(in_array($order->status, ['solicitado','aceito']))
            <button type="button"
                x-on:click="$refs.rejectModal.showModal()"
                class="inline-flex items-center px-3 py-2 rounded-md border border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white">
                Rejeitar
            </button>
        @endif

        {{-- Modal de Rejeição --}}
        <dialog x-ref="rejectModal" class="rounded-xl p-0 w-full max-w-lg backdrop:bg-black/30">
          <form method="POST" action="{{ route('cme.orders.reject', $order) }}" class="bg-white rounded-xl p-5">
            @csrf
            <h4 class="text-lg font-semibold mb-2">Motivo da rejeição</h4>
            <p class="text-sm text-gray-600 mb-3">Esta mensagem aparecerá para o solicitante.</p>
            <textarea name="observacoes" rows="4" required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-rose-600"></textarea>
            @error('observacoes')
              <p class="text-sm text-rose-700 mt-1">{{ $message }}</p>
            @enderror

            <div class="mt-4 flex justify-end gap-2">
              <button type="button" x-on:click="$refs.rejectModal.close()"
                      class="px-3 py-2 rounded-md border border-gray-300">Cancelar</button>
              <button class="px-3 py-2 rounded-md bg-rose-600 text-white">Confirmar rejeição</button>
            </div>
          </form>
        </dialog>

        @if($order->status === 'aceito')
            <form method="POST" action="{{ route('cme.orders.prep', $order) }}">
                @csrf
                <button class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition">
                    Iniciar Preparo
                </button>
            </form>
        @endif

        @if($order->status === 'em_preparo')
            <form method="POST" action="{{ route('cme.orders.ready', $order) }}">
                @csrf
                <button class="inline-flex items-center px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition">
                    Marcar como Pronto
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
