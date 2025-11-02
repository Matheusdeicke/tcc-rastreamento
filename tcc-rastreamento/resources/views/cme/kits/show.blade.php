@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">
    @if(session('ok'))
        <div class="mb-3 rounded-lg bg-emerald-50 text-emerald-700 px-4 py-2 border border-emerald-200">
            {{ session('ok') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
        <h3 class="text-2xl font-bold text-brand-800">{{ $kit->nome }}</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('kits.edit',$kit) }}"
               class="inline-flex items-center px-3 py-2 rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white text-sm font-semibold">
                Editar
            </a>
            <a href="{{ route('kits.instances.create',$kit) }}"
               class="inline-flex items-center px-3 py-2 rounded-lg bg-brand-accent hover:bg-brand-accentLight text-brand-900 text-sm font-semibold">
                + Nova instância
            </a>
            <a href="{{ route('kits.index') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
                Voltar
            </a>
        </div>
    </div>

    <p class="text-sm text-gray-600">{{ $kit->descricao ?: '—' }}</p>

    {{-- Instâncias --}}
    <h5 class="text-lg font-semibold text-brand-800 mt-6 mb-2">Instâncias</h5>
    <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left font-semibold text-brand-800">
                        <th class="px-4 py-3">Etiqueta</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Validade</th>
                        <th class="px-4 py-3">Criado</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kit->instances as $i)
                        @php
                            $badge = match($i->status){
                                'em_estoque' => 'bg-blue-50 text-blue-800',
                                'esterilizado' => 'bg-emerald-50 text-emerald-800',
                                'em_uso' => 'bg-amber-50 text-amber-800',
                                'contaminado' => 'bg-rose-50 text-rose-800',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $i->etiqueta }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ ucfirst(str_replace('_',' ',$i->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $i->data_validade? $i->data_validade->format('d/m/Y H:i') : '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $i->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2 justify-end">
                                    <a class="inline-flex items-center px-3 py-1.5 rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white text-xs font-semibold"
                                       href="{{ route('instances.edit',$i) }}">
                                        Editar
                                    </a>
                                    <form class="inline"
                                          method="POST"
                                          action="{{ route('instances.destroy',$i) }}"
                                          onsubmit="return confirm('Remover esta instância?');">
                                        @csrf @method('DELETE')
                                        <button
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg border border-rose-600 text-rose-600 hover:bg-rose-600 hover:text-white text-xs font-semibold">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sem instâncias.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Histórico de eventos (todas as instâncias) --}}
    <h5 class="text-lg font-semibold text-brand-800 mt-6 mb-2">Histórico de eventos</h5>
    <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left font-semibold text-brand-800">
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Etapa</th>
                        <th class="px-4 py-3">Local</th>
                        <th class="px-4 py-3">Observações</th>
                        <th class="px-4 py-3">Usuário</th>
                    </tr>
                </thead>
                @php
                $eventos = $kit->instances->load('eventos.user')->flatMap->eventos->sortByDesc('created_at');
                @endphp

                <tbody class="divide-y divide-gray-100">
                @forelse($eventos as $ev)
                    <tr class="hover:bg-gray-50/60">
                    <td class="px-4 py-3">{{ $ev->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $ev->etapa }}</td>
                    <td class="px-4 py-3">{{ $ev->local ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $ev->observacoes ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $ev->user?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Sem eventos registrados.</td></tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection
