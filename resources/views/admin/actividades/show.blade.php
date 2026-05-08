<x-layouts.admin :title="'Actividad'">
    @php($registrationUrl = session('actividad_registration_url') ?? $actividad->registrationUrl())

    @if($registrationUrl)
        <div class="lm-card mb-6">
            <div class="lm-card-body">
                <div class="mb-3 text-sm font-semibold text-slate-900">Formulario de compromiso</div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input readonly value="{{ $registrationUrl }}" class="lm-input font-mono text-xs">
                    <button type="button" class="lm-btn-brand" onclick="copyTextToClipboard(@js($registrationUrl))">Copiar</button>
                    <a href="{{ $registrationUrl }}" target="_blank" class="lm-btn-ghost text-center">Abrir formulario</a>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-semibold text-slate-900">{{ $actividad->nombre }}</div>
            <div class="mt-1 text-sm text-slate-600">Fecha: {{ $actividad->fecha->format('Y-m-d') }} · Estado: {{ $actividad->estado }} · Precio: $ {{ number_format((int) ($actividad->precio ?? 0), 0, ',', '.') }}</div>
        </div>

        @hasanyrole('Administrador|Supervisor')
            @if($actividad->estado !== 'cerrada')
                <form method="post" action="{{ route('admin.actividades.close', $actividad) }}" onsubmit="return confirm('¿Cerrar actividad? Los no marcados quedarán como ausentes.')">
                    @csrf
                    <button class="lm-btn-primary">Cerrar actividad</button>
                </form>
            @endif
        @endhasanyrole
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="lm-card">
            <div class="lm-card-body">
                <div class="text-xs font-semibold text-slate-500">Comprometidos</div>
                <div class="mt-2 text-2xl font-bold text-slate-900">{{ number_format((int) $compromisosCount, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="lm-card">
            <div class="lm-card-body">
                <div class="text-xs font-semibold text-slate-500">Total a pagar (global)</div>
                <div class="mt-2 text-2xl font-bold text-slate-900">$ {{ number_format((int) $resumenPagos['monto_total'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="lm-card">
            <div class="lm-card-body">
                <div class="text-xs font-semibold text-slate-500">Total pagado (global)</div>
                <div class="mt-2 text-2xl font-bold text-emerald-700">$ {{ number_format((int) $resumenPagos['monto_pagado'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="lm-card overflow-hidden">
        <div class="lm-card-header">
            <div class="text-sm font-semibold">Departamentos</div>
        </div>
        <div class="divide-y divide-slate-200">
            @foreach($departamentos as $d)
                <div class="flex items-center justify-between px-5 py-4 hover:bg-orange-50/40">
                    <div>
                        <div class="text-sm font-semibold">{{ $d->departamento->nombre }}</div>
                        <div class="text-xs text-slate-500">Estado: {{ $d->estado }} · Comprometidos: {{ $d->asistencias_count }}</div>
                        <div class="mt-1 text-xs text-slate-600">
                            Pago: {{ $d->pago_estado ?? '-' }} · $ {{ number_format((int) ($d->pago_monto_pagado ?? 0), 0, ',', '.') }} / $ {{ number_format((int) ($d->pago_monto_total ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.actividades.asistencia', $d) }}" class="lm-btn-ghost">Asistencia</a>

                        @if(((int) ($d->pago_monto_total ?? 0)) > 0)
                            @can('create', [\App\Models\FinanzasMovimiento::class, $d->departamento])
                                <form method="post" action="{{ route('admin.actividades.abonos.store', $d) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="fecha" value="{{ now()->format('Y-m-d') }}" />
                                    <input name="monto" placeholder="Abono" class="w-28 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" />
                                    <button class="lm-btn-brand">Abonar</button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
