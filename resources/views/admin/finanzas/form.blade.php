<x-layouts.admin :title="'Nuevo movimiento'">
    <form method="post" action="{{ route('admin.finanzas.store') }}" class="space-y-6">
        @csrf

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Departamento</label>
                    @if($departamentos->count() === 1)
                        <input type="hidden" name="departamento_id" value="{{ $departamentos->first()->id }}" />
                        <select class="mt-1 lm-select" disabled>
                            <option value="{{ $departamentos->first()->id }}" selected>{{ $departamentos->first()->nombre }}</option>
                        </select>
                    @else
                        <select name="departamento_id" class="mt-1 lm-select" required>
                            <option value="">-</option>
                            @foreach($departamentos as $d)
                                <option value="{{ $d->id }}" @selected((string) old('departamento_id', request('departamento_id')) === (string) $d->id)>{{ $d->nombre }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('departamento_id')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Tipo</label>
                    <select name="tipo" class="mt-1 lm-select" required>
                        <option value="ingreso" @selected(old('tipo', 'ingreso') === 'ingreso')>Ingreso</option>
                        <option value="egreso" @selected(old('tipo') === 'egreso')>Egreso</option>
                    </select>
                    @error('tipo')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Monto</label>
                    <input name="monto" value="{{ old('monto') }}" class="mt-1 lm-input" required />
                    @error('monto')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Fecha</label>
                    <input type="date" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" class="mt-1 lm-input" required />
                    @error('fecha')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Descripción</label>
                    <input name="descripcion" value="{{ old('descripcion') }}" class="mt-1 lm-input" required />
                    @error('descripcion')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.finanzas.index') }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">Guardar</button>
        </div>
    </form>
</x-layouts.admin>
