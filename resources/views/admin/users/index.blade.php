<x-layouts.admin :title="'Usuarios'">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="get" class="flex w-full gap-2 sm:max-w-md">
            <input name="q" value="{{ $q }}" placeholder="Buscar por nombre, apellido, RUT o email" class="lm-input" />
            <button class="lm-btn-brand">Buscar</button>
        </form>

        @role('Administrador')
            <a href="{{ route('admin.users.create') }}" class="lm-btn-brand">
                Nuevo usuario
            </a>
        @endrole
    </div>

    <div class="lm-card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">RUT</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($users as $user)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-4 py-3 text-sm font-medium">{{ $user->nombre }} {{ $user->apellido }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $user->rut }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $pill = match($user->estado) {
                                    'activo' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'inactivo' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-semibold {{ $pill }}">
                                {{ strtoupper($user->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <div class="flex items-center justify-end gap-3">
                                @hasanyrole('Administrador|Supervisor')
                                    @if($user->hasRole('Senderista'))
                                        <a href="{{ route('admin.users.convert', $user) }}" class="font-semibold text-orange-700 hover:text-orange-900">Convertir</a>
                                    @endif
                                @endhasanyrole

                                <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-orange-700 hover:text-orange-900">Ver</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.admin>
