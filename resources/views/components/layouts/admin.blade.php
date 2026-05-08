<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Legendario Manager') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="lm-shell">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-slate-950/40 lg:hidden" @click="sidebarOpen = false"></div>

            <aside class="lm-sidebar fixed left-0 top-0 z-40 h-screen w-64 -translate-x-full overflow-y-auto p-5 transition-transform duration-200 lg:translate-x-0" :class="{ 'translate-x-0': sidebarOpen }">
                    <div class="lm-sidebar-brand justify-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex justify-center">
                            <img src="{{ asset('images/logo-blanco.png') }}" alt="Legendarios" class="h-16 w-auto drop-shadow-sm" />
                        </a>
                    </div>

                    @php
                        $nav = [
                            [
                                'title' => 'General',
                                'items' => [
                                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'role_any' => ['Administrador', 'Supervisor']],
                                ],
                            ],
                            [
                                'title' => 'Gestión',
                                'items' => [
                                    ['label' => 'Usuarios', 'route' => 'admin.users.index', 'role_any' => ['Administrador', 'Supervisor', 'Líder']],
                                    ['label' => 'Accesos', 'route' => 'admin.accesos.index', 'role_any' => ['Administrador', 'Supervisor']],
                                    ['label' => 'Departamentos', 'route' => 'admin.departamentos.index', 'role_any' => ['Administrador', 'Supervisor', 'Líder']],
                                    ['label' => 'Actividades', 'route' => 'admin.actividades.index', 'role_any' => ['Administrador', 'Supervisor']],
                                    ['label' => 'Mis actividades', 'route' => 'admin.actividades.mis', 'role_any' => ['Administrador', 'Supervisor', 'Encargado', 'Líder']],
                                ],
                            ],
                            [
                                'title' => 'Operaciones',
                                'items' => [
                                    ['label' => 'Finanzas', 'route' => 'admin.finanzas.index'],
                                    ['label' => 'Reconocimientos', 'route' => 'admin.reconocimientos.index', 'role_any' => ['Administrador', 'Supervisor']],
                                ],
                            ],
                            [
                                'title' => 'QR',
                                'items' => [
                                    ['label' => 'Campañas QR', 'route' => 'admin.qr-campaigns.index', 'role_any' => ['Administrador', 'Supervisor']],
                                    ['label' => 'Tokens QR', 'route' => 'admin.qr-tokens.index', 'role_any' => ['Administrador', 'Supervisor']],
                                ],
                            ],
                        ];
                    @endphp

                    <nav class="mt-8 space-y-3 text-sm">
                        @foreach($nav as $section)
                            @php
                                $availableItems = collect($section['items'])
                                    ->filter(fn ($item) => \Illuminate\Support\Facades\Route::has($item['route']))
                                    ->filter(function ($item) {
                                        if (isset($item['role']) && (!auth()->user() || !auth()->user()->hasRole($item['role']))) {
                                            return false;
                                        }

                                        if (isset($item['role_any']) && (!auth()->user() || !auth()->user()->hasAnyRole($item['role_any']))) {
                                            return false;
                                        }

                                        if (auth()->user() && auth()->user()->hasRole('Finanzas') && !in_array($item['route'], ['admin.finanzas.index'], true)) {
                                            return false;
                                        }

                                        return true;
                                    });
                                $isOpen = $availableItems->contains(fn ($item) => request()->routeIs(str_replace('.index','.*',$item['route'])));
                                $sectionKey = \Illuminate\Support\Str::slug($section['title']);
                            @endphp

                            @if($availableItems->isEmpty())
                                @continue
                            @endif

                            <div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }" class="lm-sidebar-section">
                                <button type="button" class="lm-sidebar-section-trigger" @click="open = !open" :aria-expanded="open.toString()" aria-controls="sidebar-section-{{ $sectionKey }}">
                                    <span>{{ $section['title'] }}</span>
                                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="sidebar-section-{{ $sectionKey }}" x-show="open" x-transition class="mt-2 space-y-1">
                                    @foreach($availableItems as $item)
                                        <a href="{{ route($item['route']) }}" class="lm-sidebar-link {{ request()->routeIs(str_replace('.index','.*',$item['route'])) ? 'lm-sidebar-link-active' : '' }}" @click="sidebarOpen = false">
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div x-data="{ open: false }" class="lm-sidebar-section">
                            <button type="button" class="lm-sidebar-section-trigger" @click="open = !open" :aria-expanded="open.toString()" aria-controls="sidebar-section-cuenta">
                                <span>Cuenta</span>
                                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="sidebar-section-cuenta" x-show="open" x-transition class="mt-2 space-y-1">
                                <a href="{{ route('dashboard') }}" class="lm-sidebar-link" @click="sidebarOpen = false">
                                    Mi cuenta
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="px-3">
                                    @csrf
                                    <button class="w-full rounded-xl bg-white/15 px-3 py-2 text-left text-xs font-semibold text-white ring-1 ring-white/15 hover:bg-white/20">
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </nav>

                    <div class="lm-sidebar-panel">
                        <div class="text-xs font-semibold text-white/80">Acceso</div>
                        <div class="mt-1 text-xs text-white">{{ auth()->user()->email }}</div>
                    </div>
            </aside>

            <div class="min-h-screen lg:pl-64">
                <div class="flex-1">
                    <header class="lm-topbar">
                        <div class="lm-container py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <button type="button" class="lm-icon-btn lg:hidden" @click="sidebarOpen = true" aria-label="Abrir navegación">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </button>
                                    <div class="truncate text-lg font-semibold">{{ $title ?? 'Panel' }}</div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="lm-container lm-main">
                        <x-flash-messages />
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
