@php
    $alerts = [
        ['key' => 'success', 'classes' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'title' => 'Exito'],
        ['key' => 'error', 'classes' => 'border-rose-200 bg-rose-50 text-rose-800', 'title' => 'Error'],
        ['key' => 'warning', 'classes' => 'border-amber-200 bg-amber-50 text-amber-800', 'title' => 'Atencion'],
        ['key' => 'info', 'classes' => 'border-sky-200 bg-sky-50 text-sky-800', 'title' => 'Informacion'],
    ];
@endphp

<div class="space-y-3">
    @if (session('status'))
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
            <div class="font-semibold">Informacion</div>
            <div class="mt-1">{{ session('status') }}</div>
        </div>
    @endif

    @foreach ($alerts as $alert)
        @if (session($alert['key']))
            <div class="rounded-xl border px-4 py-3 text-sm {{ $alert['classes'] }}">
                <div class="font-semibold">{{ $alert['title'] }}</div>
                <div class="mt-1">{{ session($alert['key']) }}</div>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <div class="font-semibold">Revisa los datos ingresados</div>
            <ul class="mt-1 list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
