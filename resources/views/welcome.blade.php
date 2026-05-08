<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Legendario Manager') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 font-sans text-white">
        <main class="min-h-screen">
            <section class="relative flex min-h-screen items-center overflow-hidden">
                <div class="absolute inset-0">
                    <div class="h-full w-full bg-[linear-gradient(135deg,#0f172a_0%,#7c2d12_55%,#ea580c_100%)]"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.16),transparent_32%),radial-gradient(circle_at_82%_12%,rgba(253,186,116,0.22),transparent_28%)]"></div>
                </div>

                <div class="relative mx-auto grid w-full max-w-7xl grid-cols-1 gap-10 px-6 py-12 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
                    <div class="flex flex-col justify-center">
                        <div class="mb-8 flex items-center gap-3">
                            <img src="{{ asset('images/logo-blanco.png') }}" alt="Legendarios" class="h-14 w-auto" />
                            <div>
                                <div class="text-xl font-bold">Legendario Manager</div>
                                <div class="text-sm text-white/70">Gestión operativa y pastoral</div>
                            </div>
                        </div>

                        <h1 class="max-w-3xl text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                            Panel para coordinar personas, equipos, actividades y finanzas.
                        </h1>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-white/78 sm:text-lg">
                            Unifica departamentos, asistencia, campañas, reconocimientos y registros QR en una sola herramienta.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="lm-btn bg-white text-slate-950 hover:bg-orange-50">Ir a mi panel</a>
                            @else
                                <a href="{{ route('login') }}" class="lm-btn bg-white text-slate-950 hover:bg-orange-50">Iniciar sesión</a>
                            @endauth
                        </div>
                    </div>

                    <div class="flex items-end">
                        <div class="w-full rounded-2xl border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-white p-4 text-slate-950">
                                    <div class="text-xs font-semibold text-slate-500">Actividades</div>
                                    <div class="mt-3 text-3xl font-bold">360°</div>
                                </div>
                                <div class="rounded-xl bg-orange-500 p-4 text-white">
                                    <div class="text-xs font-semibold text-orange-50">Asistencia</div>
                                    <div class="mt-3 text-3xl font-bold">QR</div>
                                </div>
                                <div class="rounded-xl bg-slate-900 p-4 text-white">
                                    <div class="text-xs font-semibold text-white/60">Finanzas</div>
                                    <div class="mt-3 text-3xl font-bold">$</div>
                                </div>
                                <div class="rounded-xl bg-white p-4 text-slate-950">
                                    <div class="text-xs font-semibold text-slate-500">Roles</div>
                                    <div class="mt-3 text-3xl font-bold">8</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
