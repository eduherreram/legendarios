<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro Senderista</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <div class="text-sm font-semibold text-orange-600">Legendario Manager</div>
                <h1 class="mt-2 text-2xl font-bold tracking-tight">Registro de Senderista</h1>
                @if($campaign)
                    <div class="mt-2 text-sm font-medium text-slate-600">{{ $campaign->nombre }}</div>
                @endif
            </div>

            <x-flash-messages />

            <form method="post" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium">Nombre</label>
                        <input name="nombre" value="{{ old('nombre') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                        @error('nombre')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Apellido</label>
                        <input name="apellido" value="{{ old('apellido') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                        @error('apellido')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">RUT</label>
                        <input name="rut" value="{{ old('rut') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                        @error('rut')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Fecha nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                        @error('fecha_nacimiento')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Teléfono</label>
                        <input name="telefono" value="{{ old('telefono') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                        @error('telefono')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                        @error('email')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="mb-3 text-sm font-semibold text-slate-800">Dirección</div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium">Calle</label>
                            <input name="direccion_calle" value="{{ old('direccion_calle') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('direccion_calle')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Número</label>
                            <input name="direccion_numero" value="{{ old('direccion_numero') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('direccion_numero')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Comuna</label>
                            <input name="direccion_comuna" value="{{ old('direccion_comuna') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('direccion_comuna')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Región</label>
                            <input name="direccion_region" value="{{ old('direccion_region') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('direccion_region')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">País</label>
                            <input name="direccion_pais" value="{{ old('direccion_pais') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('direccion_pais')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="mb-3 text-sm font-semibold text-slate-800">Contacto de emergencia</div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium">Nombre</label>
                            <input name="nombre_contacto_emergencia" value="{{ old('nombre_contacto_emergencia') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('nombre_contacto_emergencia')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Parentesco</label>
                            <input name="parentesco_contacto_emergencia" value="{{ old('parentesco_contacto_emergencia') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('parentesco_contacto_emergencia')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Teléfono</label>
                            <input name="telefono_contacto_emergencia" value="{{ old('telefono_contacto_emergencia') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" required>
                            @error('telefono_contacto_emergencia')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button class="inline-flex w-full items-center justify-center rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Enviar registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
