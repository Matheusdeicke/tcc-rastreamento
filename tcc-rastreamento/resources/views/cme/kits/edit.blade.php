@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6">
  <h3 class="text-2xl font-bold text-brand-800">Editar kit</h3>

  <form method="POST" action="{{ route('kits.update',$kit) }}"
        class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-4 mt-3 space-y-4">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
      <input name="nome" required value="{{ old('nome',$kit->nome) }}"
             class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent">
      @error('nome') <small class="text-rose-600">{{ $message }}</small> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
      <textarea name="descricao" rows="3"
                class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent">{{ old('descricao',$kit->descricao) }}</textarea>
    </div>

    <div class="flex flex-wrap gap-2">
      <button class="inline-flex items-center px-4 py-2 rounded-lg bg-brand-accent hover:bg-brand-accentLight text-brand-900 text-sm font-semibold">
        Salvar
      </button>
      <a href="{{ route('kits.show',$kit) }}"
         class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
        Voltar
      </a>
    </div>
  </form>
</div>
@endsection
