@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6">
  <h3 class="text-2xl font-bold text-brand-800">Nova instância — {{ $kit->nome }}</h3>

  <form method="POST" action="{{ route('kits.instances.store',$kit) }}"
        class="bg-white rounded-xl shadow-soft ring-1 ring-black/5 p-4 mt-3 space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta</label>
      <input name="etiqueta" required value="{{ old('etiqueta') }}"
             class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent">
      @error('etiqueta') <small class="text-rose-600">{{ $message }}</small> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
      <select name="status" required
              class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent">
        @foreach($status as $st)
          <option value="{{ $st }}" @selected(old('status')===$st)>{{ $st }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Validade (se esterilizado)</label>
      <input type="datetime-local" name="data_validade" value="{{ old('data_validade') }}"
             class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent">
    </div>

    <div class="flex flex-wrap gap-2">
      <button class="inline-flex items-center px-4 py-2 rounded-lg bg-brand-accent hover:bg-brand-accentLight text-brand-900 text-sm font-semibold">
        Salvar
      </button>
      <a href="{{ route('kits.show',$kit) }}"
         class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
