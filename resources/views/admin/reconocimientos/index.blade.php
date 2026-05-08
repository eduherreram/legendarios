<x-layouts.admin :title="'Reconocimientos'">
    @if(! empty($whatsappLinks))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="text-sm font-semibold text-emerald-900">Mensajes de WhatsApp listos</div>
            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($whatsappLinks as $link)
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-white px-3 py-2 text-sm ring-1 ring-emerald-100">
                        <div class="min-w-0">
                            <div class="truncate font-semibold text-slate-800">{{ $link['name'] }}</div>
                            <div class="truncate text-xs text-slate-500">{{ $link['phone'] ?? 'Sin telefono' }}</div>
                        </div>
                        @if($link['url'])
                            <a href="{{ $link['url'] }}" target="_blank" class="shrink-0 font-semibold text-emerald-700 hover:text-emerald-900">Abrir</a>
                        @else
                            <span class="shrink-0 text-xs font-semibold text-slate-400">Sin link</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="get" class="flex w-full gap-2 sm:max-w-md">
            <input name="q" value="{{ $q }}" placeholder="Buscar reconocimiento" class="lm-input" />
            <button class="lm-btn-brand">Buscar</button>
        </form>

        @hasanyrole('Administrador|Supervisor')
            <a href="{{ route('admin.reconocimientos.create') }}" class="lm-btn-brand">Nuevo reconocimiento</a>
        @endhasanyrole
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(28rem,36rem)]">
        <section class="lm-card overflow-hidden">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Cumpleaños próximos</div>
                <div class="mt-1 text-xs text-slate-500">Se muestran usuarios activos con cumpleaños entre hoy y los próximos 30 días.</div>
            </div>

            @hasanyrole('Administrador|Supervisor')
                <form method="post" action="{{ route('admin.reconocimientos.birthdays.send') }}">
                    @csrf
                    <input type="hidden" name="titulo" value="{{ $birthdayTitle }}" />
                    <input type="hidden" name="descripcion" value="{{ $birthdayMessage }}" />

                    <div class="border-b border-slate-200 bg-white px-4 py-3">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4 text-sm">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="channels[]" value="email" class="rounded border-slate-300 text-orange-600" checked />
                                    <span>Email</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="channels[]" value="whatsapp" class="rounded border-slate-300 text-orange-600" />
                                    <span>WhatsApp</span>
                                </label>
                            </div>
                            <button class="lm-btn-brand">Felicitar seleccionados</button>
                        </div>
                    </div>
            @endhasanyrole

            <div class="overflow-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            @hasanyrole('Administrador|Supervisor')
                                <th class="w-12 px-4 py-3"></th>
                            @endhasanyrole
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Usuario</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Contacto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($birthdayUsers as $user)
                            <tr class="{{ $user->days_until_birthday === 0 ? 'bg-orange-50/60' : 'hover:bg-orange-50/40' }}">
                                @hasanyrole('Administrador|Supervisor')
                                    <td class="px-4 py-3">
                                        @if($user->days_until_birthday === 0)
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="rounded border-slate-300 text-orange-600" checked />
                                        @endif
                                    </td>
                                @endhasanyrole
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">#{{ $user->numero_legendario ?? $user->id }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div>{{ $user->next_birthday->format('d-m-Y') }}</div>
                                    <div class="mt-0.5 text-xs {{ $user->days_until_birthday === 0 ? 'font-semibold text-orange-700' : 'text-slate-500' }}">
                                        {{ $user->days_until_birthday === 0 ? 'Hoy' : 'En '.$user->days_until_birthday.' dias' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div>{{ $user->email }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">{{ $user->telefono ?: 'Sin telefono' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-sm text-slate-500">No hay cumpleaños próximos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @hasanyrole('Administrador|Supervisor')
                </form>
            @endhasanyrole
        </section>

        <section class="lm-card overflow-hidden">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Historial de envíos</div>
                <div class="mt-1 text-xs text-slate-500">Mostrando {{ $reconocimientos->count() }} de {{ $reconocimientos->total() }} reconocimientos.</div>
            </div>

            <div class="max-h-[calc(100vh-16rem)] overflow-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="sticky top-0 z-10 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Reconocimiento</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Destino</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Canales</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($reconocimientos as $r)
                            <tr class="hover:bg-orange-50/40">
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium text-slate-800">{{ $r->nombre_reconocimiento }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">{{ $r->motivo === 'cumpleanos' ? 'Cumpleaños' : 'Personalizado' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div class="font-semibold">{{ trim(($r->nombre ?? '').' '.($r->apellido ?? '')) ?: '-' }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">#{{ $r->numero_legendario }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div class="space-y-1">
                                        @if($r->enviar_email)
                                            <div>Email {{ $r->email_enviado_at ? $r->email_enviado_at->format('d-m H:i') : 'pendiente' }}</div>
                                        @endif
                                        @if($r->enviar_whatsapp)
                                            <div>WhatsApp {{ $r->whatsapp_preparado_at ? $r->whatsapp_preparado_at->format('d-m H:i') : 'pendiente' }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    @hasanyrole('Administrador|Supervisor')
                                        <form method="post" action="{{ route('admin.reconocimientos.destroy', $r) }}" onsubmit="return confirm('¿Eliminar reconocimiento?')">
                                            @csrf
                                            @method('delete')
                                            <button class="text-sm font-semibold text-slate-700 hover:text-slate-900">Eliminar</button>
                                        </form>
                                    @endhasanyrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-sm text-slate-500">No hay reconocimientos todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 bg-white px-4 py-3">
                {{ $reconocimientos->links() }}
            </div>
        </section>
    </div>
</x-layouts.admin>
