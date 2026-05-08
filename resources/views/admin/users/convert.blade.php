<x-layouts.admin :title="'Convertir usuario'">
    <div class="mb-6">
        <div class="text-sm font-semibold text-slate-900">{{ $user->nombre }} {{ $user->apellido }}</div>
        <div class="mt-1 text-sm text-slate-600">{{ $user->email }} · {{ $user->rut }}</div>
    </div>

    <x-flash-messages />

    <form method="post" action="{{ route('admin.users.convert.store', $user) }}" class="space-y-6">
        @csrf

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Número legendario</label>
                    <input name="numero_legendario" value="{{ old('numero_legendario') }}" class="mt-1 lm-input" required />
                    @error('numero_legendario')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Departamento (opcional)</label>
                    <select name="departamento_id" class="mt-1 lm-select">
                        <option value="">-</option>
                        @foreach($departamentos as $d)
                            <option value="{{ $d->id }}" @selected((string) old('departamento_id') === (string) $d->id)>{{ $d->nombre }}</option>
                        @endforeach
                    </select>
                    @error('departamento_id')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.users.show', $user) }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">Convertir a Servidor</button>
        </div>
    </form>
</x-layouts.admin>
