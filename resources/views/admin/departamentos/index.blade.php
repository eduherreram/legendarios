<x-layouts.admin :title="'Departamentos'">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="get" class="flex w-full gap-2 sm:max-w-md">
            <input name="q" value="{{ $q }}" placeholder="Buscar por nombre" class="lm-input" />
            <button class="lm-btn-brand">Buscar</button>
        </form>

        @hasanyrole('Administrador|Supervisor')
            <a href="{{ route('admin.departamentos.create') }}" class="lm-btn-brand">Nuevo departamento</a>
        @endhasanyrole
    </div>

    <div class="lm-card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Supervisor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Líder</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Encargado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($departamentos as $d)
                    <tr class="hover:bg-orange-50/40">
                        <td class="px-4 py-3 text-sm font-medium">{{ $d->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $d->supervisor?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $d->lider?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $d->encargado?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.departamentos.show', $d) }}" class="font-semibold text-orange-700 hover:text-orange-900">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $departamentos->links() }}
        </div>
    </div>
</x-layouts.admin>
