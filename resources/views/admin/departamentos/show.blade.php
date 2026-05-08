<x-layouts.admin :title="'Departamento'">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="text-sm font-semibold text-slate-900">{{ $departamento->nombre }}</div>
            <div class="mt-1 text-sm text-slate-600">
                Supervisor: {{ $departamento->supervisor?->name ?? '-' }} · Líder: {{ $departamento->lider?->name ?? '-' }} · Encargado: {{ $departamento->encargado?->name ?? '-' }}
            </div>
        </div>

        @can('update', $departamento)
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.departamentos.edit', $departamento) }}" class="lm-btn-ghost">Editar</a>

                <form method="post" action="{{ route('admin.departamentos.destroy', $departamento) }}" onsubmit="return confirm('¿Eliminar departamento?')">
                    @csrf
                    @method('delete')
                    @can('delete', $departamento)
                    <button class="lm-btn-primary">Eliminar</button>
                    @endcan
                </form>
            </div>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lm-card lg:col-span-2">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Miembros</div>
            </div>
            <div class="lm-card-body">
                <div class="divide-y divide-slate-200">
                    @forelse($departamento->usuarios as $u)
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <div class="text-sm font-semibold">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->email }} · {{ $u->rut }}</div>
                            </div>
                            <a href="{{ route('admin.users.show', $u) }}" class="text-sm font-semibold text-orange-700 hover:text-orange-900">Ver</a>
                        </div>
                    @empty
                        <div class="py-6 text-sm text-slate-600">Sin usuarios asignados.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Responsables</div>
            </div>
            <div class="lm-card-body space-y-3">
                <div>
                    <div class="text-xs font-semibold text-slate-500">Supervisor</div>
                    <div class="mt-1 text-sm">{{ $departamento->supervisor?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500">Líder</div>
                    <div class="mt-1 text-sm">{{ $departamento->lider?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500">Encargado</div>
                    <div class="mt-1 text-sm">{{ $departamento->encargado?->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
