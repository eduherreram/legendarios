<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Mi panel
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $shortcuts = [
            ['label' => 'Panel administrativo', 'route' => 'admin.dashboard', 'roles' => ['Administrador', 'Supervisor']],
            ['label' => 'Usuarios', 'route' => 'admin.users.index', 'roles' => ['Administrador', 'Supervisor', 'Líder']],
            ['label' => 'Mis actividades', 'route' => 'admin.actividades.mis', 'roles' => ['Encargado', 'Líder']],
            ['label' => 'Departamentos', 'route' => 'admin.departamentos.index', 'roles' => ['Administrador', 'Supervisor', 'Líder']],
            ['label' => 'Actividades', 'route' => 'admin.actividades.index', 'roles' => ['Administrador', 'Supervisor']],
            ['label' => 'Finanzas', 'route' => 'admin.finanzas.index', 'roles' => ['Administrador', 'Supervisor', 'Finanzas']],
            ['label' => 'Reconocimientos', 'route' => 'admin.reconocimientos.index', 'roles' => ['Administrador', 'Supervisor']],
            ['label' => 'Campañas QR', 'route' => 'admin.qr-campaigns.index', 'roles' => ['Administrador', 'Supervisor']],
        ];
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                    <div class="text-sm font-semibold text-slate-500">Bienvenido</div>
                    <div class="mt-2 text-2xl font-bold text-slate-900">{{ $user->name }}</div>
                    <div class="mt-1 text-sm text-slate-600">{{ $user->email }}</div>

                    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach($shortcuts as $shortcut)
                            @if(\Illuminate\Support\Facades\Route::has($shortcut['route']) && $user->hasAnyRole($shortcut['roles']))
                                <a href="{{ route($shortcut['route']) }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:border-orange-200 hover:bg-orange-50">
                                    {{ $shortcut['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-semibold text-slate-500">Perfil</div>
                    <div class="mt-4 space-y-3 text-sm text-slate-700">
                        <div>
                            <div class="text-xs font-semibold uppercase text-slate-400">Departamento</div>
                            <div class="mt-1">{{ $user->departamento?->nombre ?? 'Sin departamento asignado' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase text-slate-400">Roles</div>
                            <div class="mt-1">{{ $user->roles->pluck('name')->join(', ') ?: 'Sin rol asignado' }}</div>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="mt-6 inline-flex rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-700">Editar perfil</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
