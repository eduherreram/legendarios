<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AccesosController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->string('role')->toString();

        $roles = Role::query()->orderBy('name')->pluck('name')->all();

        $users = collect();
        if ($role !== '') {
            $users = User::query()
                ->with('departamento')
                ->role($role)
                ->orderBy('apellido')
                ->orderBy('nombre')
                ->get();
        }

        return view('admin.accesos.index', [
            'roles' => $roles,
            'selectedRole' => $role,
            'users' => $users,
        ]);
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'string'],
        ]);

        $user->password = $data['password'];
        $user->save();

        return redirect()->route('admin.accesos.index', [
            'role' => $data['role'] ?? '',
        ]);
    }
}
