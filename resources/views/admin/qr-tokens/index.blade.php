<x-layouts.admin :title="'Tokens QR'">
    @if($latestUrl)
        <div class="mb-6 lm-card border border-emerald-200 bg-emerald-50">
            <div class="lm-card-body">
                <div class="text-sm font-semibold text-emerald-900">Token creado</div>
                <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                    <div x-data="{ copied: false }" class="flex w-full gap-2">
                        <input readonly value="{{ $latestUrl }}" class="lm-input bg-white" onclick="this.select()" />
                        <button type="button" class="lm-icon-btn shrink-0" title="Copiar token" @click="copyTextToClipboard(@js($latestUrl)).then(() => { copied = true; setTimeout(() => copied = false, 1600) })">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                        <span x-show="copied" x-transition class="self-center text-xs font-semibold text-emerald-700">Copiado</span>
                    </div>
                    <a href="{{ $latestUrl }}" target="_blank" class="lm-btn-brand whitespace-nowrap">Abrir registro</a>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="get" class="flex w-full gap-2 sm:max-w-sm">
            <select name="status" class="lm-select">
                <option value="" @selected($status === '')>Todos</option>
                <option value="available" @selected($status === 'available')>Disponibles</option>
                <option value="used" @selected($status === 'used')>Usados</option>
                <option value="expired" @selected($status === 'expired')>Expirados</option>
            </select>
            <button class="lm-btn-brand">Filtrar</button>
        </form>

        <a href="{{ route('admin.qr-tokens.create') }}" class="lm-btn-brand">Nuevo token</a>
    </div>

    <div class="lm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Campana</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Token</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Vence</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Uso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($tokens as $token)
                        @php
                            $expired = $token->used_at === null && $token->expires_at !== null && $token->expires_at->isPast();
                            $registrationUrl = $token->registrationUrl();
                        @endphp
                        <tr class="hover:bg-orange-50/40">
                            <td class="px-4 py-3 text-sm font-medium">#{{ $token->id }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $token->campaign?->nombre ?? 'Sin campana' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                @if($registrationUrl)
                                    <div x-data="{ copied: false }" class="flex min-w-72 items-center gap-2">
                                        <input readonly value="{{ $registrationUrl }}" class="lm-input bg-white py-1 text-xs" onclick="this.select()" />
                                        <button type="button" class="lm-icon-btn h-9 w-9 shrink-0" title="Copiar token" @click="copyTextToClipboard(@js($registrationUrl)).then(() => { copied = true; setTimeout(() => copied = false, 1600) })">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                        </button>
                                        <span x-show="copied" x-transition class="text-xs font-semibold text-emerald-700">Copiado</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-500">Token antiguo no recuperable</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <span class="{{ $expired ? 'text-rose-700 font-semibold' : '' }}">
                                    {{ $token->expires_at?->format('d-m-Y H:i') ?? 'Sin vencimiento' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                @if($token->used_at)
                                    <div class="font-semibold text-emerald-700">Usado</div>
                                    <div class="text-xs">{{ $token->used_at->format('d-m-Y H:i') }} por {{ $token->usedByUser?->name ?? '-' }}</div>
                                @elseif($expired)
                                    <span class="font-semibold text-rose-700">Expirado</span>
                                @else
                                    <span class="font-semibold text-orange-700">Disponible</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No hay tokens QR registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $tokens->links() }}
        </div>
    </div>
</x-layouts.admin>
