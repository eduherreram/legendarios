<x-layouts.admin :title="'Actividades'">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="get" class="flex w-full gap-2 sm:max-w-md">
            <input name="q" value="{{ $q }}" placeholder="Buscar por nombre" class="lm-input" />
            <button class="lm-btn-brand">Buscar</button>
        </form>

        @hasanyrole('Administrador|Supervisor')
            <a href="{{ route('admin.actividades.create') }}" class="lm-btn-brand">Nueva actividad</a>
        @endhasanyrole
    </div>

    <div class="lm-card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($actividades as $a)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-4 py-3 text-sm font-medium">{{ $a->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $a->fecha->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $pill = $a->estado === 'cerrada'
                                    ? 'bg-slate-100 text-slate-700 border-slate-200'
                                    : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-semibold {{ $pill }}">
                                {{ strtoupper($a->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.actividades.show', $a) }}" class="font-semibold text-orange-700 hover:text-orange-900">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $actividades->links() }}
        </div>
    </div>
</x-layouts.admin>
