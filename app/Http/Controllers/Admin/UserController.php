<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConvertSenderistaRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Departamento;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $service,
    ) {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $authUser = $request->user();

        $users = User::query()
            ->when($authUser?->hasRole('Líder'), function ($query) use ($authUser) {
                $query->where('departamento_id', $authUser->departamento_id ?: 0);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query
                    ->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellido', 'like', "%{$q}%")
                    ->orWhere('rut', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function show(User $user): View
    {
        $authUser = auth()->user();
        if ($authUser?->hasRole('Líder') && (int) $authUser->departamento_id !== (int) $user->departamento_id) {
            abort(403);
        }

        $user->load('departamento');

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function create(): View
    {
        return $this->formView(new User());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return $this->formView($user);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->service->update($user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->route('admin.users.show', $user)->with('warning', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function convert(User $user): View
    {
        if (! $user->hasRole('Senderista')) {
            abort(404);
        }

        $departamentos = Departamento::query()->orderBy('nombre')->get();

        return view('admin.users.convert', [
            'user' => $user,
            'departamentos' => $departamentos,
        ]);
    }

    public function convertStore(ConvertSenderistaRequest $request, User $user): RedirectResponse
    {
        if (! $user->hasRole('Senderista')) {
            return redirect()->route('admin.users.show', $user)->with('warning', 'Este usuario no es Senderista.');
        }

        $data = $request->validated();

        $user->forceFill([
            'numero_legendario' => $data['numero_legendario'],
            'departamento_id' => $data['departamento_id'] ?? null,
        ])->save();

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles(['Servidor']);
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'Usuario convertido a Servidor correctamente.');
    }

    private function formView(User $user): View
    {
        $departamentos = Departamento::query()->orderBy('nombre')->get();
        $roles = Role::query()
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $selectedRole = $user->exists && method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->first()
            : null;

        return view('admin.users.form', [
            'user' => $user,
            'departamentos' => $departamentos,
            'roles' => $roles,
            'selectedRole' => $selectedRole,
        ]);
    }
}
