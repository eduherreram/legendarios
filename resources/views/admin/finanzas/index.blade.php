<x-layouts.admin :title="'Finanzas'">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start">
        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Métricas</div>
                @can('create', \App\Models\FinanzasMovimiento::class)
                    <a href="{{ route('admin.finanzas.create') }}" class="lm-btn-brand">Nuevo movimiento</a>
                @endcan
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="lm-card">
                    <div class="lm-card-body">
                        <div class="text-xs font-semibold text-slate-500">Total ingresos</div>
                        <div class="mt-2 text-3xl font-bold text-emerald-700">
                            $ {{ number_format($totales['ingresos'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="lm-card">
                    <div class="lm-card-body">
                        <div class="text-xs font-semibold text-slate-500">Total egresos</div>
                        <div class="mt-2 text-3xl font-bold text-rose-700">
                            $ {{ number_format($totales['egresos'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="lm-card">
                    <div class="lm-card-body">
                        <div class="text-xs font-semibold text-slate-500">Disponible</div>
                        <div class="mt-2 text-3xl font-bold {{ $totales['disponible'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            $ {{ number_format($totales['disponible'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="lm-card">
                <div class="lm-card-body">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-semibold text-slate-500">Balance por departamento</div>
                        <div class="text-xs text-slate-400">Click para ver detalle</div>
                    </div>
                    @php
                        $maxAbsBalance = 0;
                        foreach ($departamentos as $d) {
                            $row = $balances->get($d->id);
                            $ingresos = (int) ($row->ingresos ?? 0);
                            $egresos = (int) ($row->egresos ?? 0);
                            $balance = $ingresos - $egresos;
                            $maxAbsBalance = max($maxAbsBalance, abs($balance));
                        }
                        $maxAbsBalance = max($maxAbsBalance, 1);
                    @endphp

                    <div class="mt-3 max-h-72 space-y-2 overflow-auto pr-1">
                        @foreach($departamentos as $d)
                            @php
                                $row = $balances->get($d->id);
                                $ingresos = (int) ($row->ingresos ?? 0);
                                $egresos = (int) ($row->egresos ?? 0);
                                $balance = $ingresos - $egresos;
                                $pct = round((abs($balance) / $maxAbsBalance) * 100, 2);
                                $bar = $balance >= 0 ? 'bg-emerald-500' : 'bg-rose-500';
                                $text = $balance >= 0 ? 'text-emerald-700' : 'text-rose-700';
                            @endphp
                            <a href="{{ route('admin.finanzas.departamento', $d) }}" class="group block rounded-xl border border-slate-200 bg-white px-3 py-3 hover:bg-orange-50/40">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-slate-800">{{ $d->nombre }}</div>
                                    </div>
                                    <div class="shrink-0 text-sm font-semibold {{ $text }}">
                                        $ {{ number_format($balance, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="mt-2 h-2 w-full rounded-full bg-slate-100">
                                    <div class="h-2 rounded-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="md:sticky md:top-24 md:mt-0">
            <div class="lm-card overflow-hidden">
                <div class="lm-card-header">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-sm font-semibold">Historial</div>
                            <div class="mt-1 text-xs text-slate-500">Mostrando {{ $movimientos->count() }} de {{ $movimientos->total() }} movimientos</div>
                        </div>

                        <form method="get" class="grid w-full grid-cols-1 gap-2 sm:max-w-md sm:grid-cols-3">
                            @if($departamentos->count() === 1)
                                <input type="hidden" name="departamento_id" value="{{ $departamentos->first()->id }}" />
                                <div class="sm:col-span-2">
                                    <input type="month" name="mes" value="{{ $filters['mes'] }}" class="lm-input" />
                                </div>
                                <button class="lm-btn-brand">Filtrar</button>
                            @else
                                <select name="departamento_id" class="lm-select">
                                    <option value="">Todos</option>
                                    @foreach($departamentos as $d)
                                        <option value="{{ $d->id }}" @selected((string) $filters['departamento_id'] === (string) $d->id)>{{ $d->nombre }}</option>
                                    @endforeach
                                </select>

                                <input type="month" name="mes" value="{{ $filters['mes'] }}" class="lm-input" />

                                <button class="lm-btn-brand">Filtrar</button>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="max-h-[calc(100vh-14rem)] overflow-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 sticky top-0 z-10">
                            <tr>
                                <th class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Departamento</th>
                                <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Tipo</th>
                                <th class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Descripción</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($movimientos as $m)
                                <tr class="hover:bg-orange-50/40">
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $m->fecha->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium">{{ $m->departamento->nombre }}</td>
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
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-600">
                                        Sin movimientos para los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 bg-white px-4 py-3">
                    {{ $movimientos->links() }}
                </div>
            </div>
        </section>
    </div>
</x-layouts.admin>
