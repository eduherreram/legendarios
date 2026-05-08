<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro completado</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto max-w-2xl px-4 py-16">
        <x-flash-messages />
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-700">
                <span class="text-xl font-bold">LM</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Registro completado</h1>
            <p class="mt-2 text-sm text-slate-600">Tu inscripción fue registrada correctamente.</p>
            <p class="mt-1 text-xs text-slate-500">Si el equipo requiere validación adicional, te contactarán.</p>
        </div>
    </div>
</body>
</html>
