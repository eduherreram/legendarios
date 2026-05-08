<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="lm-shell">
            <aside class="lm-sidebar fixed left-0 top-0 h-screen w-64 overflow-y-auto p-5">
                    <div class="lm-sidebar-brand">
                        <div class="lm-sidebar-badge">LM</div>
                        <div>
                            <div class="text-sm font-semibold">Legendario Manager</div>
                            <div class="text-xs text-white/70">App</div>
                        </div>
                    </div>

                    <nav class="mt-8 space-y-6 text-sm">
                        <div>
                            <div class="lm-sidebar-section-title">General</div>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('dashboard') }}" class="lm-sidebar-link {{ request()->routeIs('dashboard') ? 'lm-sidebar-link-active' : '' }}">
                                    Dashboard
                                </a>

                                @if (\Illuminate\Support\Facades\Route::has('admin.dashboard') && auth()->check() && auth()->user()?->hasAnyRole(['Administrador', 'Supervisor', 'Líder']))
                                    <a href="{{ route('admin.dashboard') }}" class="lm-sidebar-link {{ request()->routeIs('admin.*') ? 'lm-sidebar-link-active' : '' }}">
                                        Admin
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="lm-sidebar-section-title">Cuenta</div>
                            <div class="mt-2 space-y-1">
                                @if (auth()->check())
                                    <a href="{{ route('profile.edit') }}" class="lm-sidebar-link {{ request()->routeIs('profile.*') ? 'lm-sidebar-link-active' : '' }}">
                                        Perfil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="px-3">
                                        @csrf
                                        <button class="w-full rounded-xl bg-white/15 px-3 py-2 text-left text-xs font-semibold text-white hover:bg-white/20 ring-1 ring-white/15">
                                            Cerrar sesión
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </nav>

                    <div class="lm-sidebar-panel">
                        <div class="text-xs font-semibold text-white/80">Sesión</div>
                        <div class="mt-1 text-xs text-white">{{ auth()->user()?->email ?? '-' }}</div>
                    </div>
            </aside>

            <div class="min-h-screen pl-64">
                <div class="flex-1">
                    @isset($header)
                        <header class="lm-topbar">
                            <div class="lm-container py-4">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="lm-container lm-main">
                        <x-flash-messages />
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
