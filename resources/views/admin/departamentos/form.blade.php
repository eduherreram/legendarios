<x-layouts.admin :title="($departamento->exists ? 'Editar departamento' : 'Nuevo departamento')">
    <form method="post" action="{{ $departamento->exists ? route('admin.departamentos.update', $departamento) : route('admin.departamentos.store') }}" class="space-y-6">
        @csrf
        @if($departamento->exists)
            @method('put')
        @endif

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Nombre</label>
                    <input name="nombre" value="{{ old('nombre', $departamento->nombre) }}" class="mt-1 lm-input" required />
                    @error('nombre')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Supervisor</label>
                    <select name="supervisor_id" class="mt-1 lm-select">
                        <option value="">-</option>
                        @foreach($supervisores as $u)
                            <option value="{{ $u->id }}" @selected((string) old('supervisor_id', $departamento->supervisor_id) === (string) $u->id)>{{ $u->name }} ({{ $u->rut }})</option>
                        @endforeach
                    </select>
                    @error('supervisor_id')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Líder</label>
                    <select name="lider_id" class="mt-1 lm-select">
                        <option value="">-</option>
                        @foreach($lideres as $u)
                            <option value="{{ $u->id }}" @selected((string) old('lider_id', $departamento->lider_id) === (string) $u->id)>{{ $u->name }} ({{ $u->rut }})</option>
                        @endforeach
                    </select>
                    @error('lider_id')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Encargado</label>
                    <select name="encargado_id" class="mt-1 lm-select">
                        <option value="">-</option>
                        @foreach($encargados as $u)
                            <option value="{{ $u->id }}" @selected((string) old('encargado_id', $departamento->encargado_id) === (string) $u->id)>{{ $u->name }} ({{ $u->rut }})</option>
                        @endforeach
                    </select>
                    @error('encargado_id')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.departamentos.index') }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">Guardar</button>
        </div>
    </form>
</x-layouts.admin>
