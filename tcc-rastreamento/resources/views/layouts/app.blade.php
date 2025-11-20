<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ config('app.name','CME Rastreamento') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-brand-900">
  <!-- NAVBAR -->
  <nav x-data="{open:false}" class="bg-brand-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
          <!-- Ícone -->
          <svg class="w-8 h-8 text-brand-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M4 4h16v16H4z" fill="#003566" />
            <path d="M7 13l3 3 7-7" stroke="#ffc300" stroke-width="2" fill="none" />
          </svg>
          <span class="font-semibold tracking-wide">CME Rastreamento</span>
        </a>

        @auth
          <!-- Links desktop -->
          <div class="hidden md:flex items-center gap-2 ms-6">
            {{-- ENFERMAGEM --}}
            @role('enfermagem')
              <a href="{{ route('orders.index') }}"
                class="px-3 py-2 rounded-lg border border-white/30 hover:bg-white/10 text-sm">
                Meus pedidos
              </a>

              <a href="{{ route('returns.index') }}"
                class="px-3 py-2 rounded-lg border border-white/30 hover:bg-white/10 text-sm">
                Devoluções
              </a>

              <a href="{{ route('orders.create') }}"
                class="px-3 py-2 rounded-lg bg-brand-accent hover:bg-brand-accentLight text-brand-900 text-sm font-semibold">
                Solicitar kit
              </a>
            @endrole

            {{-- CME / ADMIN --}}
            @role('cme|admin')
              <a href="{{ route('cme.orders') }}"
                class="px-3 py-2 rounded-lg bg-brand-700 hover:bg-brand-700/90 text-white text-sm font-semibold">
                CME – Pedidos
              </a>

              <a href="{{ route('cme.returns') }}"
                class="px-3 py-2 rounded-lg border border-white/30 hover:bg-white/10 text-sm">
                Devoluções
              </a>

              <a href="{{ route('kits.index') }}"
                class="px-3 py-2 rounded-lg border border-white/30 hover:bg-white/10 text-sm">
                Estoque de Kits
              </a>
            @endrole
          </div>
        @endauth
      </div>

      <div class="flex items-center gap-3">
        @auth
          <span class="hidden sm:inline text-sm text-white/80">Olá, {{ auth()->user()->name }}</span>
          <!-- Logout -->
          <form method="POST" action="{{ route('logout') }}" class="hidden md:inline">@csrf
            <button class="px-3 py-2 rounded-lg border border-white/30 hover:bg-white/10 text-sm">Sair</button>
          </form>
        @endauth

        <!-- Hamburger -->
        <button @click="open=!open" class="md:hidden inline-flex items-center justify-center p-2 rounded-md hover:bg-white/10">
          <svg class="w-6 h-6" fill="none" stroke="currentColor">
            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Menu mobile -->
    @auth
    <div x-show="open" x-cloak class="md:hidden border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-2">
        <div class="text-white/80 text-sm">Olá, {{ auth()->user()->name }}</div>

        {{-- ENFERMAGEM --}}
        @role('enfermagem')
          <a href="{{ route('orders.index') }}"
            class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm">
            Meus pedidos
          </a>

          <a href="{{ route('returns.index') }}"
            class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm">
            Devoluções
          </a>

          <a href="{{ route('orders.create') }}"
            class="px-3 py-2 rounded-lg bg-brand-accent hover:bg-brand-accentLight text-brand-900 text-sm font-semibold">
            Solicitar kit
          </a>
        @endrole

        {{-- CME / ADMIN --}}
        @role('cme|admin')
          <a href="{{ route('cme.orders') }}"
            class="px-3 py-2 rounded-lg bg-brand-700 hover:bg-brand-700/90 text-white text-sm font-semibold">
            CME – Pedidos
          </a>

          <a href="{{ route('cme.returns') }}"
            class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm">
            Devoluções
          </a>

          <a href="{{ route('kits.index') }}"
            class="px-3 py-2 rounded-lg hover:bg-white/10 text-sm">
            Estoque de Kits
          </a>
        @endrole
        <form method="POST" action="{{ route('logout') }}" class="pt-2">@csrf
          <button class="w-full text-left px-3 py-2 rounded-lg border border-white/30 hover:bg-white/10 text-sm">Sair</button>
        </form>
      </div>
    </div>
    @endauth
  </nav>

  <!-- CONTEÚDO -->
  <main class="py-6">
    @yield('content')
    {{ $slot ?? '' }}
  </main>
</body>
</html>
