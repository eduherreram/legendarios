<x-layouts.admin :title="'Accesos'">
    <div class="lm-card">
        <div class="lm-card-body space-y-4">
            <form method="get" class="grid grid-cols-1 gap-2 sm:max-w-xl sm:grid-cols-3">
                <select name="role" class="lm-select" required>
                    <option value="">Selecciona un rol</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" @selected($selectedRole === $r)>{{ $r }}</option>
                    @endforeach
                </select>

                <div class="sm:col-span-2">
                    <button class="lm-btn-brand">Ver usuarios</button>
                </div>
            </form>

            @if($selectedRole !== '')
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Usuario</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Departamento</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nueva contraseña</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($users as $u)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $u->name }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $u->email }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $u->departamento?->nombre ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <form method="post" action="{{ route('admin.accesos.password', $u) }}" class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" name="role" value="{{ $selectedRole }}" />
                                            <input type="password" name="password" class="lm-input" placeholder="Contraseña" required />
                                            <input type="password" name="password_confirmation" class="lm-input" placeholder="Confirmar" required />
                                            @error('password')
                                                <div class="sm:col-span-2 mt-1 text-xs text-red-600">{{ $message }}</div>
                                            @enderror
                                            <button class="lm-btn-brand w-full">Guardar</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-sm text-slate-500">No hay usuarios con este rol.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
