<x-layouts.admin :title="'Campañas QR'">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="get" class="flex w-full gap-2 sm:max-w-md">
            <input name="q" value="{{ $q }}" placeholder="Buscar campaña" class="lm-input" />
            <button class="lm-btn-brand">Buscar</button>
        </form>

        <a href="{{ route('admin.qr-campaigns.create') }}" class="lm-btn-brand">Nueva campaña</a>
    </div>

    <div class="lm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Vigencia</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Métricas</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($campaigns as $campaign)
                        <tr class="hover:bg-orange-50/40">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium">{{ $campaign->nombre }}</div>
                                @if($campaign->descripcion)
                                    <div class="mt-1 max-w-xl text-xs text-slate-500">{{ $campaign->descripcion }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <div>{{ $campaign->starts_at?->format('d-m-Y H:i') ?? 'Sin inicio' }}</div>
                                <div>{{ $campaign->ends_at?->format('d-m-Y H:i') ?? 'Sin cierre' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="{{ $campaign->activa ? 'text-emerald-700' : 'text-slate-500' }} font-semibold">
                                    {{ $campaign->activa ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <div>{{ $campaign->registrations_count }} registrados</div>
                                <div class="text-xs text-slate-500">{{ $campaign->tokens_usados_count }} / {{ $campaign->tokens_count }} tokens usados</div>
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.qr-campaigns.show', $campaign) }}" class="font-semibold text-orange-700 hover:text-orange-900">Historial</a>
                                    <a href="{{ route('admin.qr-campaigns.edit', $campaign) }}" class="font-semibold text-slate-700 hover:text-slate-900">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No hay campañas QR registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $campaigns->links() }}
        </div>
    </div>
</x-layouts.admin>
