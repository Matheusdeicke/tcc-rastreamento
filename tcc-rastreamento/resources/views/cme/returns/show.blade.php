@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6 space-y-4">
  <div class="flex items-center justify-between">
    <h3 class="text-2xl font-semibold text-brand-800">Devolução #{{ $returnRequest->id }} — CME</h3>
    <a href="{{ route('cme.returns') }}"
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

  <div class="bg-white rounded-2xl shadow-soft ring-1 ring-black/5 p-4 space-x-2">
    @if($returnRequest->status === 'return_requested')
      <form method="POST" action="{{ route('cme.returns.confirm',$returnRequest) }}" class="inline">@csrf
        <button class="px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">Confirmar recebimento</button>
      </form>
    @endif

    {{--  @if(in_array($returnRequest->status, ['return_requested','received_by_cme']))
      <form method="POST" action="{{ route('cme.returns.quarantine',$returnRequest) }}" class="inline">@csrf
        <button class="px-3 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold">Quarentena</button>
      </form>
    @endif --}}

    @if(in_array($returnRequest->status, ['received_by_cme','quarantine']))
      <form method="POST" action="{{ route('cme.returns.reprocess',$returnRequest) }}" class="inline">@csrf
        <button class="px-3 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">Enviar p/ reprocesso</button>
      </form>
    @endif

    @if(in_array($returnRequest->status, ['reprocessing','quarantine','received_by_cme']))
      <form method="POST" action="{{ route('cme.returns.release',$returnRequest) }}" class="inline">@csrf
        <button class="px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Liberar ao estoque</button>
      </form>
    @endif
  </div>

  {{-- Conferência de peças pela CME --}}
  @if($returnRequest->kitInstance->kit?->items && $returnRequest->kitInstance->kit->items->count())
    <div class="bg-white rounded-2xl shadow-soft ring-1 ring-black/5 overflow-hidden">
      <div class="p-4 flex items-center justify-between">
        <div class="font-semibold text-brand-800">Conferência de peças do kit</div>
        @if(in_array($returnRequest->status, ['received_by_cme','quarantine','reprocessing']))
          <span class="text-xs text-gray-500">Edite os campos e clique em "Registrar conferência".</span>
        @else
          <span class="text-xs text-gray-500">Conferência somente para consulta (devolução já concluída).</span>
        @endif
      </div>

      <form method="POST" action="{{ route('cme.returns.check-items', $returnRequest) }}">
        @csrf

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm divide-y divide-gray-100">
            <thead class="bg-gray-50">
              <tr>
                <th class="p-3 text-left">Peça</th>
                <th class="p-3 text-center">Qtd. esperada</th>
                <th class="p-3 text-center">Qtd. conferida</th>
                <th class="p-3 text-center">Situação</th>
                <th class="p-3 text-left">Observações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach($returnRequest->kitInstance->kit->items as $item)
                @php
                  $check = $returnRequest->checkItems->firstWhere('kit_item_id', $item->id);
                @endphp
                <tr>
                  <td class="p-3">{{ $item->nome }}</td>
                  <td class="p-3 text-center">
                    {{ $item->quantidade }}
                    <input type="hidden"
                          name="items[{{ $item->id }}][expected_qty]"
                          value="{{ $item->quantidade }}">
                  </td>
                  <td class="p-3 text-center">
                    <input type="number" min="0"
                          name="items[{{ $item->id }}][returned_qty]"
                          value="{{ old("items.{$item->id}.returned_qty", $check->returned_qty ?? $item->quantidade) }}"
                          @disabled(!in_array($returnRequest->status, ['received_by_cme','quarantine','reprocessing']))
                          class="w-20 rounded-lg border-gray-300 text-sm text-center">
                  </td>
                  <td class="p-3 text-center">
                    @php $status = old("items.{$item->id}.status", $check->status ?? 'ok'); @endphp
                    <select name="items[{{ $item->id }}][status]"
                            @disabled(!in_array($returnRequest->status, ['received_by_cme','quarantine','reprocessing']))
                            class="rounded-lg border-gray-300 text-sm">
                      <option value="ok"        @selected($status === 'ok')>OK</option>
                      <option value="faltando"  @selected($status === 'faltando')>Faltando</option>
                      <option value="danificado"@selected($status === 'danificado')>Danificado</option>
                    </select>
                  </td>
                  <td class="p-3">
                    <input type="text"
                          name="items[{{ $item->id }}][observacoes]"
                          value="{{ old("items.{$item->id}.observacoes", $check->observacoes ?? '') }}"
                          @disabled(!in_array($returnRequest->status, ['received_by_cme','quarantine','reprocessing']))
                          class="w-full rounded-lg border-gray-300 text-sm">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if(in_array($returnRequest->status, ['received_by_cme','quarantine','reprocessing']))
          <div class="flex justify-end px-4 py-3 bg-gray-50 border-t border-gray-100">
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold">
              Registrar conferência
            </button>
          </div>
        @endif
      </form>
    </div>
  @endif

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
                <td class="p-3">{{ $it->kitItem->nome ?? '—' }}</td>
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
