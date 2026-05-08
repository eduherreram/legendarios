<x-layouts.admin :title="'Usuario'">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="text-sm font-semibold text-slate-900">{{ $user->nombre }} {{ $user->apellido }}</div>
            <div class="mt-1 text-sm text-slate-600">{{ $user->email }} · {{ $user->rut }}</div>
        </div>

        <div class="flex items-center gap-2">
            @hasanyrole('Administrador|Supervisor')
                @if($user->hasRole('Senderista'))
                    <a href="{{ route('admin.users.convert', $user) }}" class="lm-btn-brand">Convertir</a>
                @endif

                <a href="{{ route('admin.users.edit', $user) }}" class="lm-btn-ghost">
                    Editar
                </a>

                <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar usuario?')">
                    @csrf
                    @method('delete')
                    <button class="lm-btn-primary">
                        Eliminar
                    </button>
                </form>
            @endhasanyrole
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lm-card lg:col-span-2">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Datos</div>
            </div>
            <div class="lm-card-body">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Número legendario</dt>
                    <dd class="mt-1 text-sm">{{ $user->numero_legendario ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Estado</dt>
                    <dd class="mt-1 text-sm">{{ $user->estado }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Fecha inscripción</dt>
                    <dd class="mt-1 text-sm">{{ optional($user->fecha_inscripcion)->format('Y-m-d H:i') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Departamento</dt>
                    <dd class="mt-1 text-sm">{{ $user->departamento?->nombre ?? '-' }}</dd>
                </div>
                </dl>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Roles</div>
            </div>
            <div class="lm-card-body">
                <div class="flex flex-wrap gap-2">
                @foreach($user->getRoleNames() as $role)
                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">{{ $role }}</span>
                @endforeach
                @if($user->getRoleNames()->isEmpty())
                    <span class="text-sm text-slate-600">Sin rol</span>
                @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
