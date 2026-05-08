<x-layouts.admin :title="$campaign->exists ? 'Editar campaña QR' : 'Nueva campaña QR'">
    <form method="post" action="{{ $campaign->exists ? route('admin.qr-campaigns.update', $campaign) : route('admin.qr-campaigns.store') }}" class="space-y-6">
        @csrf
        @if($campaign->exists)
            @method('put')
        @endif

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Nombre</label>
                    <input name="nombre" value="{{ old('nombre', $campaign->nombre) }}" class="mt-1 lm-input" required />
                    @error('nombre')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Descripción</label>
                    <textarea name="descripcion" rows="4" class="mt-1 lm-input">{{ old('descripcion', $campaign->descripcion) }}</textarea>
                    @error('descripcion')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Inicio</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $campaign->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 lm-input" />
                    @error('starts_at')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Cierre</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $campaign->ends_at?->format('Y-m-d\TH:i')) }}" class="mt-1 lm-input" />
                    @error('ends_at')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <label class="flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="activa" value="1" class="rounded border-slate-300 text-orange-600" @checked(old('activa', $campaign->exists ? $campaign->activa : true)) />
                    Campaña activa
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.qr-campaigns.index') }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">Guardar</button>
        </div>
    </form>
</x-layouts.admin>
