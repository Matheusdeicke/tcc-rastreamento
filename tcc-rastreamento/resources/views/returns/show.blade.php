@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6 space-y-4">
  <div class="flex items-center justify-between">
    <h3 class="text-2xl font-semibold text-brand-800">Devolução #{{ $returnRequest->id }}</h3>
    <a href="{{ route('returns.index') }}"
       class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
      Voltar
    </a>
  </div>

  @if(session('ok'))
    <div class="rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  <div class="bg-white rounded-2xl shadow-soft ring-1 ring-black/5 divide-y">
    <div class="p-4">
      <div class="text-sm text-gray-500">Status</div>
      <div class="font-medium">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
          {{ $returnRequest->status_label }}
        </span>
      </div>
    </div>
    <div class="p-4">
      <div class="text-sm text-gray-500">Kit / Etiqueta</div>
      <div class="font-medium">
        {{ $returnRequest->kitInstance->kit->nome ?? '—' }}
        <span class="ms-2 font-mono text-gray-700">{{ $returnRequest->kitInstance->etiqueta }}</span>
      </div>
    </div>
    <div class="p-4 grid grid-cols-2 gap-4">
      <div>
        <div class="text-sm text-gray-500">Solicitante</div>
        <div class="font-medium">{{ $returnRequest->requester->name ?? '—' }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Solicitado em</div>
        <div class="font-medium">{{ optional($returnRequest->requested_at)->format('d/m/Y H:i') }}</div>
      </div>
    </div>
    @if($returnRequest->notes)
      <div class="p-4">
        <div class="text-sm text-gray-500">Observações</div>
        <div class="font-medium whitespace-pre-wrap">{{ $returnRequest->notes }}</div>
      </div>
    @endif
  </div>

  @if($returnRequest->items && $returnRequest->items->count())
    <div class="bg-white rounded-2xl shadow-soft ring-1 ring-black/5 overflow-hidden">
      <div class="p-4 font-semibold text-brand-800">Itens reportados</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm divide-y divide-gray-100">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-3 text-left">Item</th>
              <th class="p-3">Status</th>
              <th class="p-3">Qtd</th>
              <th class="p-3 text-left">Obs</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($returnRequest->items as $it)
              <tr>
                <td class="p-3">{{ $it->kit_item_id ?? '—' }}</td>
                <td class="p-3">{{ $it->reported_status }}</td>
                <td class="p-3 text-center">{{ $it->reported_qty }}</td>
                <td class="p-3">{{ $it->notes }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>
@endsection
