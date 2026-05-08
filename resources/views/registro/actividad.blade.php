<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Compromiso Actividad</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto max-w-xl px-4 py-10">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <div class="text-sm font-semibold text-orange-600">Legendario Manager</div>
                <h1 class="mt-2 text-2xl font-bold tracking-tight">{{ $actividad->nombre }}</h1>
                <div class="mt-2 text-sm font-medium text-slate-600">
                    Fecha: {{ $actividad->fecha->format('Y-m-d') }}
                </div>
            </div>

            <x-flash-messages />

            <form method="post" class="space-y-5">
                @csrf

                <div>
                    <label class="text-sm font-medium">Numero legendario</label>
                    <input name="numero_legendario" value="{{ old('numero_legendario') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required autofocus>
                    @error('numero_legendario')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <button class="inline-flex w-full items-center justify-center rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Confirmar compromiso
                </button>
            </form>
        </div>
    </div>
</body>
</html>
