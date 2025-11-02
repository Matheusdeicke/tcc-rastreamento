@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">
    @if (session('ok'))
        <div class="mb-3 rounded-lg bg-emerald-50 text-emerald-700 px-4 py-2 border border-emerald-200">{{ session('ok') }}</div>
    @endif
    @if (session('erro'))
        <div class="mb-3 rounded-lg bg-rose-50 text-rose-700 px-4 py-2 border border-rose-200">{{ session('erro') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <h3 class="text-2xl font-bold text-brand-800">Kits (Catálogo)</h3>
        <a href="{{ route('kits.create') }}"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-brand-accent hover:bg-brand-accentLight text-brand-900 text-sm font-semibold">
            + Novo kit
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($kits as $kit)
            <div class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 h-full flex flex-col">
                <div class="p-4 flex-1">
                    <h5 class="text-lg font-semibold text-brand-800">{{ $kit->nome }}</h5>
                    <p class="text-sm text-gray-600 mt-1">{{ $kit->descricao ?: '—' }}</p>
                </div>
                <div class="px-4 pb-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('kits.show', $kit) }}"
                           class="inline-flex items-center px-3 py-2 rounded-lg border border-brand-700 text-brand-700 hover:bg-brand-700 hover:text-white text-sm font-semibold">
                            Abrir
                        </a>

                        <form action="{{ route('kits.destroy', $kit) }}" method="POST"
                              onsubmit="return confirm('Tem certeza que deseja excluir este kit?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                              class="inline-flex items-center px-3 py-2 rounded-lg border border-rose-600 text-rose-600 hover:bg-rose-600 hover:text-white text-sm font-semibold
                                     disabled:opacity-60 disabled:cursor-not-allowed"
                              @if ($kit->tem_instancia_nao_devolvida) disabled @endif>
                                Excluir Kit
                            </button>
                        </form>
                    </div>

                    @if ($kit->tem_instancia_nao_devolvida)
                        <small class="block mt-2 text-amber-600">
                            Este kit possui instâncias não devolvidas e não pode ser excluído.
                        </small>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center p-10 bg-white rounded-xl shadow-soft ring-1 ring-black/5">
                    Nenhum kit cadastrado. <a href="{{ route('kits.create') }}" class="underline">Cadastrar</a>.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $kits->links() }}</div>
</div>
@endsection
