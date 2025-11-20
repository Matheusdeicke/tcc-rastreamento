@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-4 sm:p-6">
    <h3 class="text-2xl font-bold text-brand-800 mb-4">
        Editar peça do kit: {{ $kit->nome }}
    </h3>

    <form method="POST"
          action="{{ route('kits.items.update', [$kit, $item]) }}"
          class="space-y-4 bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome da peça</label>
            <input type="text" name="nome" value="{{ old('nome', $item->nome) }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700"
                   required>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                <input type="text" name="codigo" value="{{ old('codigo', $item->codigo) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                <input type="number" name="quantidade" min="1"
                       value="{{ old('quantidade', $item->quantidade) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700"
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <input type="text" name="observacoes" value="{{ old('observacoes', $item->observacoes) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand-700">
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('kits.show', $kit) }}"
               class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-brand-700 text-white hover:bg-brand-800 font-semibold">
                Salvar alterações
            </button>
        </div>
    </form>
</div>
@endsection
