<x-layouts.admin :title="'Nueva actividad'">
    <form method="post" action="{{ route('admin.actividades.store') }}" class="space-y-6">
        @csrf

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Nombre</label>
                    <input name="nombre" value="{{ old('nombre') }}" class="mt-1 lm-input" required />
                    @error('nombre')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Fecha</label>
                    <input type="date" name="fecha" value="{{ old('fecha') }}" class="mt-1 lm-input" required />
                    @error('fecha')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Precio</label>
                    <input name="precio" value="{{ old('precio', 0) }}" class="mt-1 lm-input" />
                    @error('precio')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div class="sm:col-span-2 text-sm text-slate-600">
                    Al crear una actividad se genera un formulario publico de compromiso. La asistencia se crea solo para los servidores que se inscriban con su numero legendario.
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.actividades.index') }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">Crear actividad</button>
        </div>
    </form>
</x-layouts.admin>
