<x-layouts.admin :title="'Mis actividades'">
    <div class="lm-card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Actividad</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($items as $i)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-4 py-3 text-sm font-medium">{{ $i->actividad->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $i->actividad->fecha->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $pill = $i->actividad->estado === 'cerrada'
                                    ? 'bg-slate-100 text-slate-700 border-slate-200'
                                    : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-semibold {{ $pill }}">
                                {{ strtoupper($i->actividad->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.actividades.asistencia', $i) }}" class="lm-btn-brand">Marcar asistencia</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-sm text-slate-500">
                            No hay actividades asignadas a tu departamento todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $items->links() }}
        </div>
    </div>
</x-layouts.admin>
