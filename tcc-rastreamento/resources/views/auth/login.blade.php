<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-2xl font-bold mb-1">Entrar</h1>
    <p class="text-sm text-brand-700/80 mb-6">Acesse sua conta para continuar.</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" class="block mt-1 w-full"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox"
                       class="rounded border-brand-700 text-brand-accent shadow-sm focus:ring-brand-accent"
                       name="remember">
                <span class="text-sm text-brand-700">{{ __('Lembrar de mim') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm underline underline-offset-4 text-brand-700 hover:text-brand-900"
                   href="{{ route('password.request') }}">
                    {{ __('Esqueci minha senha') }}
                </a>
            @endif
        </div>

        <div class="pt-2 flex items-center justify-end">
            <x-primary-button class="ms-3">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>

        <div class="text-sm text-brand-700/90 pt-4">
            <span>Ainda não possui conta?</span>
            <a class="font-medium text-brand-900 hover:underline underline-offset-4"
               href="{{ route('register') }}">Criar conta</a>
        </div>
    </form>
</x-guest-layout>
