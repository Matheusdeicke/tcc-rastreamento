@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
  <h3 class="text-2xl font-bold text-brand-800 mb-6">Registrar devolução</h3>

  @if ($errors->any())
    <div class="mb-4 p-3 rounded-xl border border-rose-200 bg-rose-50 text-rose-700">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('returns.store') }}"
        class="bg-white rounded-2xl shadow-soft ring-1 ring-black/5 p-6 space-y-5">
    @csrf

    <div>
      <label class="block text-sm font-medium text-brand-800 mb-1">Etiqueta da instância</label>
      <input name="etiqueta" value="{{ old('etiqueta', request('etiqueta')) }}" required
        placeholder="Ex.: KIT-TRAUMA-001"
        class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm">
    </div>

    <div>
      <label class="block text-sm font-medium text-brand-800 mb-1">Observações</label>
      <textarea name="notes" rows="3"
                class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm"
                placeholder="Ex.: 1 pinça faltante, caixa íntegra.">{{ old('notes') }}</textarea>
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <a href="{{ route('returns.index') }}"
         class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
        Cancelar
      </a>
      <button type="submit"
              class="inline-flex items-center px-4 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold">
        Enviar devolução
      </button>
    </div>
  </form>
</div>
@endsection
