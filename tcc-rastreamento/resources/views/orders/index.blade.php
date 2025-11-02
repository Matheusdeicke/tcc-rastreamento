@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <h3 class="text-2xl font-semibold text-brand-800">Meus pedidos</h3>
    <a href="{{ route('orders.create') }}"
       class="inline-flex items-center px-4 py-2 rounded-xl bg-brand-accent text-brand-900 font-semibold hover:bg-brand-accentLight transition">
      + Solicitar kit
    </a>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  @if($orders->count())
    <div class="overflow-x-auto bg-white rounded-2xl shadow-soft ring-1 ring-black/5">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
          <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Setor</th>
            <th class="px-4 py-3">Data solicitada</th>
            <th class="px-4 py-3">Criado em</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
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

          @foreach($orders as $order)
            <tr class="hover:bg-gray-50/60">
              <td class="px-4 py-3 font-medium text-brand-900">{{ $order->id }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                  {{ str($order->status)->replace('_',' ')->title() }}
                </span>
              </td>
              <td class="px-4 py-3 text-brand-900">{{ $order->setor ?? '—' }}</td>
              <td class="px-4 py-3 text-brand-900">{{ optional($order->needed_at)->format('d/m/Y H:i') ?? '—' }}</td>
              <td class="px-4 py-3 text-brand-900">{{ $order->created_at->format('d/m/Y H:i') }}</td>
              <td class="px-4 py-3">
                <a href="{{ route('orders.show', $order) }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white transition">
                  Ver detalhes
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $orders->links() }}
    </div>
  @else
    <div class="text-center p-10 rounded-2xl border border-dashed border-brand-700/20 bg-white/70 text-brand-800">
      <p class="mb-4">Você ainda não possui pedidos.</p>
      <a href="{{ route('orders.create') }}"
         class="inline-flex items-center px-4 py-2 rounded-xl bg-brand-accent text-brand-900 font-semibold hover:bg-brand-accentLight transition">
        Solicitar primeiro kit
      </a>
    </div>
  @endif
</div>
@endsection
