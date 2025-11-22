@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  <h3 class="text-2xl font-semibold text-brand-800 mb-4">Detalhes do Pedido #{{ $order->id }}</h3>

  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  @php
    $statusClasses = [
      'solicitado'       => 'bg-amber-100 text-amber-800',
      'aceito'           => 'bg-blue-100 text-blue-800',
      'em_preparo'       => 'bg-sky-100 text-sky-800',
      'pronto_para_envio'=> 'bg-indigo-100 text-indigo-800',
      'entregue'         => 'bg-emerald-100 text-emerald-800',
      'fechado'          => 'bg-gray-200 text-gray-800',
      'recusado'         => 'bg-rose-100 text-rose-800',
    ];
  @endphp

  <div class="bg-white rounded-2xl shadow-soft p-6 ring-1 ring-black/5 mb-4">
    <div class="flex flex-wrap items-center gap-2 mb-3">
      <span class="text-sm text-gray-600">Status:</span>
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
        {{ str($order->status)->replace('_',' ')->title() }}
      </span>
    </div>

    <div class="grid md:grid-cols-2 gap-4 text-sm">
      <p><span class="font-medium text-brand-800">Solicitante:</span> {{ $order->requester->name ?? '—' }}</p>
      <p><span class="font-medium text-brand-800">Setor:</span> {{ $order->setor ?? '—' }}</p>
      <p class="md:col-span-2">
      <span class="font-medium text-brand-800">Material solicitado:</span>
      {{ $order->requestedKit?->nome ?? '—' }}
      </p>
      <p class="md:col-span-2">
        <span class="font-medium text-brand-800">Observações:</span>
        <span class="text-brand-900">{{ $order->observacoes ?? '—' }}</span>
      </p>
      <p class="md:col-span-2">
        <span class="font-medium text-brand-800">Data solicitada:</span>
        {{ optional($order->needed_at)?->format('d/m/Y H:i') ?? '—' }}
      </p>
    </div>
  </div>

  @if($order->allocations->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-soft p-6 ring-1 ring-black/5 mb-4">
      <h5 class="text-brand-800 font-semibold mb-3">Kit alocado</h5>
      @foreach($order->allocations as $alloc)
        <div class="mb-3 last:mb-0">
          <p class="text-sm">
            <span class="text-brand-800 font-medium">Etiqueta:</span>
            <span class="font-semibold text-brand-900">{{ $alloc->kitInstance->etiqueta }}</span>
            <span class="ms-2 text-xs inline-flex px-2 py-0.5 rounded-full bg-gray-100 text-gray-800">
              {{ $alloc->kitInstance->status }}
            </span>
          </p>
          <p class="text-xs text-gray-600">
            Reservado em: {{ optional($alloc->reserved_at)?->format('d/m/Y H:i') ?? '—' }}
          </p>
        </div>
      @endforeach
    </div>
  @endif

  @php
    $inst = $order->allocations->last()->kitInstance ?? null;
    $eventos = $inst?->eventos ?? collect();
  @endphp

  @php
    // só pode devolver se:
    // - existe instância
    // - pedido está entregue
    // - NÃO há devolução em aberto para essa instância
    $podeDevolver = $inst
      && $order->status === 'entregue'
      && ! $inst->openReturn;
  @endphp

  @if($inst && auth()->user()->hasRole('enfermagem'))
    <div class="mt-4 mb-4">
      @if($podeDevolver)
        <a href="{{ route('returns.create', ['etiqueta' => $inst->etiqueta]) }}"
          class="inline-flex items-center px-4 py-2 rounded-xl bg-brand-accent text-brand-900 font-semibold hover:bg-brand-accentLight transition"
          title="Registrar devolução deste kit">
          Devolver material ({{ $inst->etiqueta }})
        </a>
      @else
        <button type="button"
          class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-200 text-gray-600 font-semibold cursor-not-allowed"
          title="Já existe uma devolução em andamento para este kit">
          Devolução em andamento ({{ $inst->etiqueta }})
        </button>
      @endif
    </div>
  @endif

  @if($inst)
    <div class="bg-white rounded-2xl shadow-soft p-6 ring-1 ring-black/5">
      <h5 class="text-brand-800 font-semibold mb-3">Rastreabilidade do kit ({{ $inst->etiqueta }})</h5>
      <ul class="divide-y divide-gray-100">
        @forelse($eventos as $ev)
          <li class="py-3">
            <div class="flex flex-wrap items-center gap-2">
              <span class="font-semibold text-brand-900">{{ str($ev->etapa)->title() }}</span>
              <span class="text-gray-500">— {{ $ev->local ?? '—' }}</span>
              <small class="text-gray-500">| {{ $ev->created_at->format('d/m/Y H:i') }}</small>
            </div>
            @if($ev->observacoes)
              <div class="text-sm text-gray-600 mt-1">{{ $ev->observacoes }}</div>
            @endif
          </li>
        @empty
          <li class="py-4 text-gray-500">Nenhum evento registrado ainda.</li>
        @endforelse
      </ul>
    </div>
  @endif

  @if($order->status === 'recusado')
  @php $motivo = $order->lastRejectionReason(); @endphp
  <div class="mb-3 rounded-md border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3">
      <div class="font-semibold">Pedido rejeitado pela CME</div>
      <div class="text-sm mt-1 whitespace-pre-line">
        {{ $motivo ?? 'Sem motivo informado.' }}
      </div>
  </div>
  @endif


  <div class="mt-4">
    <a href="{{ route('orders.index') }}"
       class="inline-flex items-center px-4 py-2 rounded-xl border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white transition">
      Voltar
    </a>
  </div>
</div>
@endsection
