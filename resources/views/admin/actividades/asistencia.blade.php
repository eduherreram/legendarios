<x-layouts.admin :title="'Asistencia'">
    @php
        $precio = (int) ($departamentoActividad->actividad->precio ?? 0);
    @endphp

    <div class="mb-6">
        <div class="text-sm font-semibold text-slate-900">
            {{ $departamentoActividad->actividad->nombre }} · {{ $departamentoActividad->departamento->nombre }}
        </div>
        <div class="mt-1 text-sm text-slate-600">Fecha: {{ $departamentoActividad->actividad->fecha->format('Y-m-d') }} · Valor por persona: $ {{ number_format($precio, 0, ',', '.') }}</div>
    </div>

    <div class="lm-card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Usuario</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Pago</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($asistencias as $a)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-4 py-3 text-sm font-medium">{{ $a->user->name }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $pill = $a->estado === 'presente'
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                    : 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-semibold {{ $pill }}">
                                {{ strtoupper($a->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $pagoEstado = $a->pago_estado ?? 'pendiente';
                                $pagoPill = match($pagoEstado) {
                                    'pagado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'abonado' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'freepass' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <div class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-semibold {{ $pagoPill }}">
                                {{ strtoupper($pagoEstado) }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                $ {{ number_format((int) ($a->pago_monto_pagado ?? 0), 0, ',', '.') }} / $ {{ number_format($pagoEstado === 'freepass' ? 0 : $precio, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            @can('update', $a)
                                @if($departamentoActividad->actividad->estado === 'cerrada')
                                    <span class="text-xs font-semibold text-slate-400">CERRADA</span>
                                @else
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <form method="post" action="{{ route('admin.actividades.asistencia.update', ['departamentoActividad' => $departamentoActividad->id, 'asistencia' => $a->id]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="presente" />
                                            <button class="lm-btn-brand">Presente</button>
                                        </form>
                                        <form method="post" action="{{ route('admin.actividades.asistencia.update', ['departamentoActividad' => $departamentoActividad->id, 'asistencia' => $a->id]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="ausente" />
                                            <button class="lm-btn-ghost">Ausente</button>
                                        </form>
                                        @if($precio > 0)
                                            <form method="post" action="{{ route('admin.actividades.asistencia.update', ['departamentoActividad' => $departamentoActividad->id, 'asistencia' => $a->id]) }}">
                                                @csrf
                                                <input type="hidden" name="estado" value="{{ $a->estado }}" />
                                                <input type="hidden" name="pago_accion" value="total" />
                                                <button class="lm-btn-brand">Paga total</button>
                                            </form>
                                            <form method="post" action="{{ route('admin.actividades.asistencia.update', ['departamentoActividad' => $departamentoActividad->id, 'asistencia' => $a->id]) }}" class="flex items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="estado" value="{{ $a->estado }}" />
                                                <input type="hidden" name="pago_accion" value="abono" />
                                                <input name="monto" placeholder="Abono" class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200" />
                                                <button class="lm-btn-ghost">Abonar</button>
                                            </form>
                                        @endif
                                        <form method="post" action="{{ route('admin.actividades.asistencia.update', ['departamentoActividad' => $departamentoActividad->id, 'asistencia' => $a->id]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="{{ $a->estado }}" />
                                            <input type="hidden" name="pago_accion" value="freepass" />
                                            <button class="lm-btn-ghost">FREEPASS</button>
                                        </form>
                                    </div>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">
                            No hay servidores comprometidos para este departamento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
