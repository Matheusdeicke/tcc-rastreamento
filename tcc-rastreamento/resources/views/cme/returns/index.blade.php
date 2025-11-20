@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="flex items-baseline justify-between mb-4">
    <div>
      <h3 class="text-2xl font-bold text-brand-800">Devoluções — CME</h3>
      <p class="text-sm text-brand-700/80">Recebimento, reprocesso e liberação</p>
    </div>
  </div>

  @if(session('ok'))
    <div class="mb-3 rounded-lg bg-emerald-50 text-emerald-700 px-4 py-2 border border-emerald-200">{{ session('ok') }}</div>
  @endif

  <div class="overflow-x-auto bg-white rounded-2xl shadow-soft ring-1 ring-black/5">
    <table class="min-w-full divide-y divide-gray-100">
      <thead class="bg-gray-50">
        <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">Kit / Etiqueta</th>
          <th class="px-4 py-3">Solicitante</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Solicitado em</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 text-sm">
        @forelse($returns as $ret)
          <tr class="hover:bg-gray-50/60">
            <td class="px-4 py-3 font-medium text-brand-900">#{{ $ret->id }}</td>
            <td class="px-4 py-3">
              {{ $ret->kitInstance->kit->nome ?? '—' }}
              <span class="ms-2 font-mono text-gray-700">{{ $ret->kitInstance->etiqueta }}</span>
            </td>
            <td class="px-4 py-3">{{ $ret->requester->name ?? '—' }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                {{ $ret->status_label }}
              </span>
            </td>
            <td class="px-4 py-3 text-brand-900">{{ optional($ret->requested_at)->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-3">
              <a href="{{ route('cme.returns.show', $ret) }}"
                 class="inline-flex items-center px-3 py-1.5 rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white transition">
                Processar
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-10 text-center text-gray-500">Sem devoluções.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $returns->links() }}
  </div>
</div>
@endsection
