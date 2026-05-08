<x-layouts.admin :title="'Dashboard'">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lm-card">
            <div class="lm-card-body">
                <div class="text-xs font-semibold text-slate-500">Departamentos</div>
                <div class="mt-2 text-3xl font-bold">{{ $totalDepartamentos }}</div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-body">
                <div class="text-xs font-semibold text-slate-500">Usuarios activos</div>
                <div class="mt-2 text-3xl font-bold text-emerald-700">{{ $usuariosActivos }}</div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-body">
                <div class="text-xs font-semibold text-slate-500">Usuarios inactivos</div>
                <div class="mt-2 text-3xl font-bold text-slate-700">{{ $usuariosInactivos }}</div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-body">
                <div class="text-xs font-semibold text-slate-500">Balance global</div>
                <div class="mt-2 text-3xl font-bold text-orange-700">$ {{ number_format($balanceGlobal, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lm-card lg:col-span-2">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Participacion en actividades</div>
            </div>
            <div class="lm-card-body">
                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                        <div class="text-xs font-semibold text-emerald-700">Presentes</div>
                        <div class="mt-1 text-lg font-bold text-emerald-900">{{ number_format($totalesAsistencia['presentes']) }}</div>
                    </div>
                    <div class="rounded-xl border border-rose-100 bg-rose-50 p-3">
                        <div class="text-xs font-semibold text-rose-700">Ausentes</div>
                        <div class="mt-1 text-lg font-bold text-rose-900">{{ number_format($totalesAsistencia['ausentes']) }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <div class="text-xs font-semibold text-slate-600">Depto. actividad</div>
                        <div class="mt-1 text-lg font-bold text-slate-900">{{ number_format($totalesAsistencia['actividades']) }}</div>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($participacionPorActividad as $item)
                        @php
                            $total = (int) $item->presentes + (int) $item->ausentes;
                            $pct = $total > 0 ? (int) round(((int) $item->presentes / $total) * 100) : 0;
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs text-slate-600">
                                <span class="font-semibold text-slate-700">{{ $item->nombre }}</span>
                                <span>{{ $item->presentes }} presentes / {{ $item->ausentes }} ausentes</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Aun no hay asistencias registradas.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Resumen operativo</div>
            </div>
            <div class="lm-card-body">
                <div class="grid grid-cols-1 gap-3">
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                        <div class="text-xs font-semibold text-emerald-700">Ingresos</div>
                        <div class="mt-1 text-lg font-bold text-emerald-900">$ {{ number_format($ingresos, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-xl border border-rose-100 bg-rose-50 p-3">
                        <div class="text-xs font-semibold text-rose-700">Egresos</div>
                        <div class="mt-1 text-lg font-bold text-rose-900">$ {{ number_format($egresos, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <div class="text-xs font-semibold text-slate-600">Actividades</div>
                        <div class="mt-1 text-lg font-bold text-slate-900">{{ number_format($totalActividades) }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ number_format($actividadesAbiertas) }} abiertas / {{ number_format($actividadesCerradas) }} cerradas</div>
                    </div>
                    <div class="rounded-xl border border-orange-100 bg-orange-50 p-3">
                        <div class="text-xs font-semibold text-orange-700">QR activos</div>
                        <div class="mt-1 text-lg font-bold text-orange-900">{{ number_format($campanasActivas) }} campanas</div>
                        <div class="mt-1 text-xs text-orange-800">{{ number_format($tokensDisponibles) }} tokens disponibles</div>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-200 pt-4">
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Ultimos movimientos</div>
                    <div class="space-y-2">
                        @forelse($ultimosMovimientos as $mov)
                            <div class="text-xs text-slate-600">
                                <span class="font-semibold {{ $mov->tipo === 'ingreso' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ strtoupper($mov->tipo) }}
                                </span>
                                <span class="mx-1">/</span>
                                <span>{{ $mov->departamento?->nombre ?? 'Sin departamento' }}</span>
                                <span class="mx-1">/</span>
                                <span>$ {{ number_format((int) $mov->monto, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500">Sin datos recientes.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
