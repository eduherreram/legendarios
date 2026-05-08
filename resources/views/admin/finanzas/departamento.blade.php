<x-layouts.admin :title="'Finanzas por departamento'">
    <div class="mb-6 flex flex-col gap-3">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-sm font-semibold text-slate-900">{{ $departamento->nombre }}</div>
                <div class="mt-1 text-sm text-slate-600">Detalle de movimientos y resumen (según filtros)</div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.finanzas.index') }}" class="lm-btn-ghost">Volver</a>
                @can('create', \App\Models\FinanzasMovimiento::class)
                    <a href="{{ route('admin.finanzas.create', ['departamento_id' => $departamento->id]) }}" class="lm-btn-brand">Nuevo movimiento</a>
                @endcan
            </div>
        </div>

        <form method="get" class="grid w-full grid-cols-1 gap-2 sm:max-w-3xl sm:grid-cols-4">
            <select name="tipo" class="lm-select">
                <option value="">Todos</option>
                <option value="ingreso" @selected($filters['tipo'] === 'ingreso')>Ingreso</option>
                <option value="egreso" @selected($filters['tipo'] === 'egreso')>Egreso</option>
            </select>
            <input type="date" name="desde" value="{{ $filters['desde'] }}" class="lm-input" />
            <input type="date" name="hasta" value="{{ $filters['hasta'] }}" class="lm-input" />
            <button class="lm-btn-brand">Filtrar</button>
        </form>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="lm-card">
                <div class="lm-card-body">
                    <div class="text-xs font-semibold text-slate-500">Ingresos</div>
                    <div class="mt-2 text-2xl font-bold text-emerald-700">$ {{ number_format($resumen['ingresos'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="lm-card">
                <div class="lm-card-body">
                    <div class="text-xs font-semibold text-slate-500">Egresos</div>
                    <div class="mt-2 text-2xl font-bold text-rose-700">$ {{ number_format($resumen['egresos'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="lm-card">
                <div class="lm-card-body">
                    <div class="text-xs font-semibold text-slate-500">Balance</div>
                    <div class="mt-2 text-2xl font-bold {{ $resumen['balance'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        $ {{ number_format($resumen['balance'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lm-card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Tipo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Monto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Descripción</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($movimientos as $m)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $m->fecha->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $pill = $m->tipo === 'ingreso'
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                    : 'bg-rose-50 text-rose-700 border-rose-200';
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-semibold {{ $pill }}">
                                {{ strtoupper($m->tipo) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold {{ $m->tipo === 'ingreso' ? 'text-emerald-700' : 'text-rose-700' }}">
                            $ {{ number_format($m->monto, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $m->descripcion }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            @can('delete', $m)
                                <form method="post" action="{{ route('admin.finanzas.destroy', $m) }}" onsubmit="return confirm('¿Eliminar movimiento?')">
                                    @csrf
                                    @method('delete')
                                    <button class="text-sm font-semibold text-slate-700 hover:text-slate-900">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $movimientos->links() }}
        </div>
    </div>
</x-layouts.admin>
