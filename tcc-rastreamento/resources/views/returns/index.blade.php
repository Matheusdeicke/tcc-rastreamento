@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <h3 class="text-2xl font-semibold text-brand-800">Minhas devoluções</h3>
    <a href="{{ route('returns.create') }}"
       class="inline-flex items-center px-4 py-2 rounded-xl bg-brand-accent text-brand-900 font-semibold hover:bg-brand-accentLight transition">
      + Registrar devolução
    </a>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  <div class="overflow-x-auto bg-white rounded-2xl shadow-soft ring-1 ring-black/5">
    <table class="min-w-full divide-y divide-gray-100">
      <thead class="bg-gray-50">
        <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">Kit / Etiqueta</th>
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
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                {{ $ret->status_label }}
              </span>
            </td>
            <td class="px-4 py-3 text-brand-900">{{ optional($ret->requested_at)->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-3">
              <a href="{{ route('returns.show',$ret) }}"
                 class="inline-flex items-center px-3 py-1.5 rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white transition">
                Ver
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-10 text-center text-gray-500">Nenhuma devolução registrada.</td>
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
