<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Du hast dein Passwort vergessen? Kein Problem. Gib hier einfach deine E-Mail-Adresse ein und dir wird anschließend ein Link zum Zurücksetzen deines Passworts gesendet.') }}
        </div>

        <div class="text-red-500">
            Diese Funktion ist aktuell nicht funktionsfähig, da kein Mail-Server vorhanden ist.
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('E-Mail') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Link senden') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
