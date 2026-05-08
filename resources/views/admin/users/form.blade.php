<x-layouts.admin :title="($user->exists ? 'Editar usuario' : 'Nuevo usuario')">
    <form method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="space-y-6">
        @csrf
        @if($user->exists)
            @method('put')
        @endif

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-medium">Número legendario</label>
                <input name="numero_legendario" value="{{ old('numero_legendario', $user->numero_legendario) }}" class="mt-1 lm-input" />
                @error('numero_legendario')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Estado</label>
                <select name="estado" class="mt-1 lm-select" required>
                    @foreach(['activo' => 'Activo', 'inactivo' => 'Inactivo', 'pendiente' => 'Pendiente'] as $k => $v)
                        <option value="{{ $k }}" @selected(old('estado', $user->estado ?: 'activo') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                @error('estado')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Nombre</label>
                <input name="nombre" value="{{ old('nombre', $user->nombre) }}" class="mt-1 lm-input" required />
                @error('nombre')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Apellido</label>
                <input name="apellido" value="{{ old('apellido', $user->apellido) }}" class="mt-1 lm-input" required />
                @error('apellido')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">RUT</label>
                <input name="rut" value="{{ old('rut', $user->rut) }}" class="mt-1 lm-input" required />
                @error('rut')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Fecha nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->fecha_nacimiento)->format('Y-m-d')) }}" class="mt-1 lm-input" required />
                @error('fecha_nacimiento')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Teléfono</label>
                <input name="telefono" value="{{ old('telefono', $user->telefono) }}" class="mt-1 lm-input" />
                @error('telefono')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 lm-input" required />
                @error('email')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="enfermedad" value="1" class="rounded border-slate-300 text-orange-600 focus:ring-orange-200" @checked(old('enfermedad', (bool) $user->enfermedad))>
                <label class="text-sm font-medium">Enfermedad</label>
                @error('enfermedad')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="es_pastor" value="1" class="rounded border-slate-300 text-orange-600 focus:ring-orange-200" @checked(old('es_pastor', (bool) $user->es_pastor))>
                <label class="text-sm font-medium">Es pastor</label>
                @error('es_pastor')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Talla</label>
                <select name="talla" class="mt-1 lm-select">
                    <option value="">-</option>
                    @foreach(['S','M','L','XL','XXL'] as $t)
                        <option value="{{ $t }}" @selected(old('talla', $user->talla) === $t)>{{ $t }}</option>
                    @endforeach
                </select>
                @error('talla')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Iglesia</label>
                <input name="iglesia" value="{{ old('iglesia', $user->iglesia) }}" class="mt-1 lm-input" />
                @error('iglesia')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Departamento</label>
                <select name="departamento_id" class="mt-1 lm-select">
                    <option value="">-</option>
                    @foreach($departamentos as $d)
                        <option value="{{ $d->id }}" @selected((string) old('departamento_id', $user->departamento_id) === (string) $d->id)>{{ $d->nombre }}</option>
                    @endforeach
                </select>
                @error('departamento_id')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-medium">Rol</label>
                <select name="role" class="mt-1 lm-select" required>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" @selected(old('role', $selectedRole) === $r)>{{ $r }}</option>
                    @endforeach
                </select>
                @error('role')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-medium">Calle</label>
                <input name="direccion_calle" value="{{ old('direccion_calle', $user->direccion_calle) }}" class="mt-1 lm-input" />
                @error('direccion_calle')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">Número</label>
                <input name="direccion_numero" value="{{ old('direccion_numero', $user->direccion_numero) }}" class="mt-1 lm-input" />
                @error('direccion_numero')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">Comuna</label>
                <input name="direccion_comuna" value="{{ old('direccion_comuna', $user->direccion_comuna) }}" class="mt-1 lm-input" />
                @error('direccion_comuna')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">Región</label>
                <input name="direccion_region" value="{{ old('direccion_region', $user->direccion_region) }}" class="mt-1 lm-input" />
                @error('direccion_region')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">País</label>
                <input name="direccion_pais" value="{{ old('direccion_pais', $user->direccion_pais) }}" class="mt-1 lm-input" />
                @error('direccion_pais')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-medium">Contacto emergencia</label>
                <input name="nombre_contacto_emergencia" value="{{ old('nombre_contacto_emergencia', $user->nombre_contacto_emergencia) }}" class="mt-1 lm-input" required />
                @error('nombre_contacto_emergencia')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">Parentesco</label>
                <input name="parentesco_contacto_emergencia" value="{{ old('parentesco_contacto_emergencia', $user->parentesco_contacto_emergencia) }}" class="mt-1 lm-input" required />
                @error('parentesco_contacto_emergencia')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">Teléfono emergencia</label>
                <input name="telefono_contacto_emergencia" value="{{ old('telefono_contacto_emergencia', $user->telefono_contacto_emergencia) }}" class="mt-1 lm-input" required />
                @error('telefono_contacto_emergencia')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">
                Guardar
            </button>
        </div>
    </form>
</x-layouts.admin>
