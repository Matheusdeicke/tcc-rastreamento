@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
  <h3 class="text-2xl font-bold text-brand-800 mb-6">Solicitar Kit</h3>

  @if(session('ok'))
    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 shadow-sm">
      {{ session('ok') }}
    </div>
  @endif

  <form action="{{ route('orders.store') }}" method="POST"
        class="bg-white rounded-2xl shadow-soft ring-1 ring-black/5 p-6 space-y-5">
    @csrf

    {{-- Material desejado --}}
    <div>
      <label class="block text-sm font-medium text-brand-800 mb-1">
        Material desejado (opcional)
      </label>

      <div class="flex gap-2 items-center">
        <select id="kit_id"
                name="requested_kit_id"
                class="flex-1 rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm">
          <option value="">— Não especificar —</option>
          @foreach($kits as $kit)
            <option
              value="{{ $kit->id }}"
              @selected(old('requested_kit_id')==$kit->id)
              data-nome="{{ $kit->nome }}"
              data-items='@json($kit->items)'
            >
              {{ $kit->nome }}
            </option>
          @endforeach
        </select>
        <button type="button"
                id="btnVerPecas"
                onclick="openKitItemsModal()"
                class="px-3 py-2 rounded-lg border border-brand-700 text-brand-700 text-sm font-semibold disabled:opacity-40 disabled:cursor-not-allowed"
                disabled>
          Ver peças
        </button>
      </div>

      @error('requested_kit_id')
        <p class="text-sm text-rose-700 mt-1">{{ $message }}</p>
      @enderror
    </div>


    {{-- Data e hora --}}
    <div>
      <label class="block text-sm font-medium text-brand-800 mb-1">
        Data e hora de necessidade
      </label>
      <input type="datetime-local" name="needed_at"
             value="{{ old('needed_at') }}"
             class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm">
      @error('needed_at')
        <p class="text-sm text-rose-700 mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Setor --}}
    <div>
      <label class="block text-sm font-medium text-brand-800 mb-1">
        Setor / Sala
      </label>
      <input type="text" name="setor"
             value="{{ old('setor') }}"
             placeholder="Ex: Centro Cirúrgico - Sala 2"
             class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm">
      @error('setor')
        <p class="text-sm text-rose-700 mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Observações --}}
    <div>
      <label class="block text-sm font-medium text-brand-800 mb-1">
        Observações
      </label>
      <textarea name="observacoes" rows="3"
                class="w-full rounded-lg border-gray-300 focus:border-brand-accent focus:ring-brand-accent text-sm">{{ old('observacoes') }}</textarea>
      @error('observacoes')
        <p class="text-sm text-rose-700 mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Botões --}}
    <div class="flex justify-end gap-3 pt-2">
      <a href="{{ route('orders.index') }}"
         class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition">
        Cancelar
      </a>
      <button type="submit"
              class="inline-flex items-center px-4 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold transition">
        Enviar solicitação
      </button>
    </div>
  </form>
</div>

@include('orders.partials.kit-items-modal')
@endsection

