@extends('layouts.app')

@section('content')
  <div class="max-w-5xl mx-auto px-4">
    <h1 class="text-2xl font-bold mb-1">Gerenciar usuários</h1>
    <p class="text-sm text-gray-600 mb-4">
      Altere o papel dos usuários entre enfermagem, CME e admin.
    </p>

    @if(session('success'))
      <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-2 text-sm">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-2 text-sm">
        {{ session('error') }}
      </div>
    @endif

    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left font-semibold text-gray-700">Nome</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-700">E-mail</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-700">Papel atual</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-700">Alterar papel</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($users as $user)
          @php
            $currentRole = $user->roles->first()->name ?? '—';
          @endphp
          <tr>
            <td class="px-4 py-2">{{ $user->name }}</td>
            <td class="px-4 py-2 text-gray-700">{{ $user->email }}</td>
            <td class="px-4 py-2">
              <span class="inline-flex px-2 py-1 rounded-full text-xs
                @if($currentRole === 'admin')
                  bg-purple-100 text-purple-800
                @elseif($currentRole === 'cme')
                  bg-blue-100 text-blue-800
                @else
                  bg-emerald-100 text-emerald-800
                @endif
              ">
                {{ ucfirst($currentRole) }}
              </span>
            </td>
            <td class="px-4 py-2">
              <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')

                <select name="role"
                        class="border-gray-300 rounded-lg text-sm">
                  @foreach($availableRoles as $role)
                    <option value="{{ $role }}" @selected($currentRole === $role)>
                      {{ ucfirst($role) }}
                    </option>
                  @endforeach
                </select>

                <button type="submit"
                        class="px-3 py-1.5 rounded-lg bg-brand-800 text-white text-xs font-semibold hover:bg-brand-700">
                  Atualizar
                </button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $users->links() }}
    </div>
  </div>
@endsection
