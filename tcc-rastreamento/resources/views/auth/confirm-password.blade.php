<x-guest-layout>
    <h1 class="text-2xl font-bold mb-2">Confirmar senha</h1>
    <p class="text-sm text-brand-700/80 mb-6">
        Esta é uma área segura do sistema. Confirme sua senha para continuar.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end">
            <x-primary-button>{{ __('Confirmar') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
