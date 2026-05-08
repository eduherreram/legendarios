<x-layouts.admin :title="'Historial QR'">
    @if(session('qr_token_url'))
        <div class="mb-6 lm-card border border-emerald-200 bg-emerald-50">
            <div class="lm-card-body">
                <div class="text-sm font-semibold text-emerald-900">Token inicial creado</div>
                <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                    <div x-data="{ copied: false }" class="flex w-full gap-2">
                        <input readonly value="{{ session('qr_token_url') }}" class="lm-input bg-white" onclick="this.select()" />
                        <button type="button" class="lm-icon-btn shrink-0" title="Copiar token" @click="copyTextToClipboard(@js(session('qr_token_url'))).then(() => { copied = true; setTimeout(() => copied = false, 1600) })">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                        <span x-show="copied" x-transition class="self-center text-xs font-semibold text-emerald-700">Copiado</span>
                    </div>
                    <a href="{{ session('qr_token_url') }}" target="_blank" class="lm-btn-brand whitespace-nowrap">Abrir registro</a>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="text-2xl font-bold text-slate-900">{{ $campaign->nombre }}</div>
            @if($campaign->descripcion)
                <div class="mt-1 max-w-3xl text-sm text-slate-600">{{ $campaign->descripcion }}</div>
            @endif
            <div class="mt-2 text-xs text-slate-500">
                {{ $campaign->starts_at?->format('d-m-Y H:i') ?? 'Sin inicio' }} /
                {{ $campaign->ends_at?->format('d-m-Y H:i') ?? 'Sin cierre' }}
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.qr-tokens.create', ['qr_campaign_id' => $campaign->id]) }}" class="lm-btn-brand">Generar token</a>
            <a href="{{ route('admin.qr-campaigns.index') }}" class="lm-btn-ghost">Volver</a>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Registrados</div><div class="mt-2 text-2xl font-bold">{{ $metrics['registrados'] }}</div></div></div>
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Convertidos</div><div class="mt-2 text-2xl font-bold text-emerald-700">{{ $metrics['convertidos'] }}</div></div></div>
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Conversion</div><div class="mt-2 text-2xl font-bold">{{ $metrics['conversion_pct'] }}%</div></div></div>
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Con tribu</div><div class="mt-2 text-2xl font-bold text-indigo-700">{{ $metrics['con_tribu'] }} <span class="text-sm text-slate-500">({{ $metrics['con_tribu_pct'] }}%)</span></div></div></div>
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Tribus</div><div class="mt-2 text-2xl font-bold">{{ $metrics['tribus'] }}</div></div></div>
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Tokens</div><div class="mt-2 text-2xl font-bold">{{ $metrics['tokens_total'] }}</div></div></div>
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Disponibles</div><div class="mt-2 text-2xl font-bold text-orange-700">{{ $metrics['tokens_disponibles'] }}</div></div></div>
        <div class="lm-card"><div class="lm-card-body"><div class="text-xs font-semibold text-slate-500">Expirados</div><div class="mt-2 text-2xl font-bold text-rose-700">{{ $metrics['tokens_expirados'] }}</div></div></div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lm-card lg:col-span-2">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Tribus de esta campana</div>
            </div>
            <div class="lm-card-body">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @forelse($tribus as $tribu)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">{{ $tribu->nombre }}</div>
                                    @if($tribu->descripcion)
                                        <div class="mt-1 text-xs text-slate-500">{{ $tribu->descripcion }}</div>
                                    @endif
                                    <div class="mt-2 text-xs font-semibold text-orange-700">{{ $tribu->members_count }} integrantes</div>
                                </div>
                                <form method="post" action="{{ route('admin.qr-campaigns.tribus.destroy', [$campaign, $tribu]) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="text-xs font-semibold text-rose-700 hover:text-rose-900">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Todavia no hay tribus creadas para esta campana.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lm-card">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Nueva tribu</div>
            </div>
            <div class="lm-card-body">
                <form method="post" action="{{ route('admin.qr-campaigns.tribus.store', $campaign) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium">Nombre</label>
                        <input name="nombre" value="{{ old('nombre') }}" class="mt-1 lm-input" required />
                        @error('nombre')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Descripcion</label>
                        <textarea name="descripcion" rows="3" class="mt-1 lm-input">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <button class="lm-btn-brand w-full">Crear tribu</button>
                </form>
            </div>
        </div>
    </div>

    <div class="mb-6 lm-card overflow-hidden">
        <div class="lm-card-header">
            <div class="text-sm font-semibold">Tokens de registro</div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">URL</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Vence</th>
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
                                @if($token->used_at)
                                    <span class="font-semibold text-emerald-700">Usado</span>
                                @elseif($expired)
                                    <span class="font-semibold text-rose-700">Expirado</span>
                                @else
                                    <span class="font-semibold text-orange-700">Disponible</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $token->expires_at?->format('d-m-Y H:i') ?? 'Sin vencimiento' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No hay tokens asociados a esta campana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="lm-card overflow-hidden">
        <div class="lm-card-header">
            <div class="text-sm font-semibold">Registrados por esta campana</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Contacto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Rol actual</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Departamento</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Tribu campana</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Registro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($registrations as $registration)
                        <tr class="hover:bg-orange-50/40">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $registration->user) }}" class="text-sm font-semibold text-orange-700 hover:text-orange-900">
                                    {{ $registration->user->name }}
                                </a>
                                <div class="text-xs text-slate-500">{{ $registration->user->rut }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <div>{{ $registration->user->email }}</div>
                                <div class="text-xs text-slate-500">{{ $registration->user->telefono }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $registration->user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $registration->user->departamento?->nombre ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <form method="post" action="{{ route('admin.qr-campaigns.registrations.tribu', [$campaign, $registration]) }}" class="flex min-w-52 gap-2">
                                    @csrf
                                    @method('put')
                                    <select name="tribu_id" class="lm-select py-1 text-xs">
                                        <option value="">Sin tribu</option>
                                        @foreach($tribus as $tribu)
                                            <option value="{{ $tribu->id }}" @selected((int) ($registration->tribuMember?->tribu_id) === (int) $tribu->id)>{{ $tribu->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button class="lm-btn-brand px-3 py-1 text-xs">Guardar</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $registration->registered_at?->format('d-m-Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Todavia no hay registros asociados a esta campana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $registrations->links() }}
        </div>
    </div>
</x-layouts.admin>
