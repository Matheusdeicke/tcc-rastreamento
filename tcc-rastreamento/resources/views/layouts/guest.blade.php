<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'Aplicação') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-brand-900 via-brand-800 to-brand-700 text-white">
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo / Título (Opção 2) -->
      <div class="text-center mb-6">
        <a href="{{ url('/') }}" class="inline-flex flex-col items-center">
          <svg class="w-12 h-12 text-brand-accent mb-2" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4 4h16v16H4z" fill="#003566" />
            <path d="M7 13l3 3 7-7" stroke="#ffc300" stroke-width="2" fill="none" />
          </svg>
          <span class="text-xl font-semibold tracking-wide text-white">Sistema de Pedidos CME</span>
          <span class="text-sm text-white/70">Central de Material e Esterilização</span>
        </a>
      </div>

      <!-- Card -->
      <div class="bg-white/95 text-brand-900 backdrop-blur rounded-2xl shadow-soft p-6 ring-1 ring-white/50">
        {{ $slot }}
      </div>

      <p class="mt-6 text-center text-sm text-white/70">
        © {{ date('Y') }} {{ config('app.name') }} • Segurança e rastreabilidade
      </p>
    </div>
  </div>
</body>
</html>
