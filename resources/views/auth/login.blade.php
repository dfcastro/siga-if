<x-guest-layout>
    {{-- Adicionando a logo com um espaçamento inferior --}}

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" placeholder="Digite seu email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" placeholder="Digite sua senha" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-ifnmg-green shadow-sm focus:ring-ifnmg-green" name="remember">
                <span class="ms-2 text-sm text-gray-600">Lembrar Usuário</span>
            </label>
        </div>

        {{-- MELHORIA: Campo do reCAPTCHA centralizado e com margem --}}
        <div class="mt-4 flex justify-center">
            {!! NoCaptcha::display() !!}
        </div>

        {{-- MELHORIA: Exibição de erro específica para o reCAPTCHA --}}
        @if ($errors->has('g-recaptcha-response'))
            <div class="text-sm text-red-600 mt-2 text-center">
                Por favor, confirme que você não é um robô.
            </div>
        @endif


        <div class="mt-6">
            {{-- MELHORIA: Botão com largura total --}}
            <x-primary-button class="w-full justify-center py-3">
                ENTRAR
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>