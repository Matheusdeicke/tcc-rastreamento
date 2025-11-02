<x-guest-layout>
    <h1 class="text-2xl font-bold mb-2">Redefinir senha</h1>
    <p class="text-sm text-brand-700/80 mb-6">
        Informe seu e-mail e enviaremos um link para redefinição de senha.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" class="block mt-1 w-full"
                type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>{{ __('Enviar link de redefinição') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
