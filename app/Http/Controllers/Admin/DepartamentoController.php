<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartamentoRequest;
use App\Http\Requests\Admin\UpdateDepartamentoRequest;
use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartamentoController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $authUser = $request->user();

        $departamentos = Departamento::query()
            ->with(['supervisor', 'lider', 'encargado'])
            ->when($authUser?->hasRole('Líder'), function ($query) use ($authUser) {
                $query->where('id', $authUser->departamento_id ?: 0);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%");
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.departamentos.index', [
            'departamentos' => $departamentos,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return $this->formView(new Departamento());
    }

    public function store(StoreDepartamentoRequest $request): RedirectResponse
    {
        Departamento::query()->create($request->validated());

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento creado correctamente.');
    }

    public function show(Departamento $departamento): View
    {
        $this->authorize('view', $departamento);

        $departamento->load(['supervisor', 'lider', 'encargado', 'usuarios']);

        return view('admin.departamentos.show', [
            'departamento' => $departamento,
        ]);
    }

    public function edit(Departamento $departamento): View
    {
        $this->authorize('update', $departamento);

        return $this->formView($departamento);
    }

    public function update(UpdateDepartamentoRequest $request, Departamento $departamento): RedirectResponse
    {
        $departamento->forceFill($request->validated())->save();

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Departamento $departamento): RedirectResponse
    {
        $this->authorize('delete', $departamento);

        $departamento->delete();

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento eliminado correctamente.');
    }

    private function formView(Departamento $departamento): View
    {
        $base = User::query()
            ->where('estado', 'activo')
            ->orderBy('apellido')
            ->orderBy('nombre');

        $supervisores = (clone $base)->role('Supervisor')->get();
        $lideres = (clone $base)->role('Líder')->get();
        $encargados = (clone $base)->role('Encargado')->get();

        return view('admin.departamentos.form', [
            'departamento' => $departamento,
            'supervisores' => $supervisores,
            'lideres' => $lideres,
            'encargados' => $encargados,
        ]);
    }
}
