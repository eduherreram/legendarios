<x-layouts.admin :title="'Nuevo token QR'">
    <form method="post" action="{{ route('admin.qr-tokens.store') }}" class="space-y-6">
        @csrf

        <div class="lm-card">
            <div class="lm-card-body grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Campaña</label>
                    <select name="qr_campaign_id" class="mt-1 lm-select">
                        <option value="">Sin campaña</option>
                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign->id }}" @selected((string) old('qr_campaign_id', $selectedCampaignId) === (string) $campaign->id)>
                                {{ $campaign->nombre }} {{ $campaign->activa ? '' : '(inactiva)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('qr_campaign_id')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Vencimiento</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', now()->addDays(7)->format('Y-m-d\TH:i')) }}" class="mt-1 lm-input" />
                    @error('expires_at')<div class="mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.qr-tokens.index') }}" class="lm-btn-ghost">Cancelar</a>
            <button class="lm-btn-brand">Generar token</button>
        </div>
    </form>
</x-layouts.admin>
