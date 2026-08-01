<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-1">Bienvenido de nuevo</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Ingresa a la plataforma de Aerolínea Trompillo.</p>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-emerald-600">
            {{ session('status') }}
        </div>
    @endif
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="email" value="Correo electrónico" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            </div>
            <div>
                <x-label for="password" value="Contraseña" />
                <x-input id="password" type="password" name="password" required autocomplete="current-password" />
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <div class="mr-1">
                    <a class="text-sm underline hover:no-underline" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif
            <x-button class="ml-3">
                Ingresar
            </x-button>
        </div>
    </form>
    <x-validation-errors class="mt-4" />
</x-authentication-layout>
