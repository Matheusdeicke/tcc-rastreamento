<x-guest-layout>
    <h1 class="text-2xl font-bold mb-2">Verifique seu e-mail</h1>
    <p class="text-sm text-brand-700/80">
        Obrigado por se cadastrar! Enviamos um link de verificação para o seu e-mail.
        Se você não recebeu, podemos enviar outro.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 text-sm font-medium text-green-700">
            Um novo link de verificação foi enviado para o e-mail informado no cadastro.
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>{{ __('Reenviar e-mail de verificação') }}</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="text-sm underline underline-offset-4 text-brand-700 hover:text-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-accent focus:ring-offset-2 focus:ring-offset-white rounded-md">
                {{ __('Sair') }}
            </button>
        </form>
    </div>
</x-guest-layout>
