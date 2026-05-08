<x-layouts.admin :title="'Nuevo reconocimiento'">
    <form method="post" action="{{ route('admin.reconocimientos.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Título</label>
                    <input name="titulo" value="{{ old('titulo') }}" class="mt-1 lm-input" required />
                    @error('titulo')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Descripción</label>
                    <textarea name="descripcion" rows="5" class="mt-1 lm-input" required>{{ old('descripcion') }}</textarea>
                    @error('descripcion')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Imagen</label>
                    <input type="file" name="imagen" accept="image/*" class="mt-1 lm-input" />
                    @error('imagen')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Canales</label>
                    <div class="mt-3 flex flex-wrap gap-4 text-sm">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="channels[]" value="email" class="rounded border-slate-300 text-orange-600" @checked(in_array('email', old('channels', ['email']), true)) />
                            <span>Email</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="channels[]" value="whatsapp" class="rounded border-slate-300 text-orange-600" @checked(in_array('whatsapp', old('channels', []), true)) />
                            <span>WhatsApp</span>
                        </label>
                    </div>
                    @error('channels')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="lm-card overflow-hidden">
            <div class="lm-card-header">
                <div class="text-sm font-semibold">Destinatarios</div>
                <div class="mt-1 text-xs text-slate-500">Selecciona uno o varios usuarios registrados.</div>
                @error('user_ids')<div class="mt-2 text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div class="max-h-[28rem] overflow-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="sticky top-0 z-10 bg-slate-50">
                        <tr>
                            <th class="w-12 px-4 py-3"></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Usuario</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Contacto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-orange-50/40">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="rounded border-slate-300 text-orange-600" @checked(in_array($user->id, old('user_ids', []))) />
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">#{{ $user->numero_legendario ?? $user->id }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div>{{ $user->email }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">{{ $user->telefono ?: 'Sin telefono' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.reconocimientos.index') }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">Enviar reconocimiento</button>
        </div>
    </form>
</x-layouts.admin>
