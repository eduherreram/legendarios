<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $fullName = trim((string) $request->string('name'));
        $parts = preg_split('/\s+/', $fullName) ?: [];
        $nombre = $parts[0] ?? 'Usuario';
        $apellido = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : 'Legendario';

        $user = User::create([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'rut' => $this->generateUniqueRut(),
            'fecha_nacimiento' => '1990-01-01',
            'enfermedad' => false,
            'es_pastor' => false,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nombre_contacto_emergencia' => 'Pendiente',
            'parentesco_contacto_emergencia' => 'Otro',
            'telefono_contacto_emergencia' => '000000000',
            'estado' => 'pendiente',
            'fecha_inscripcion' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('success', 'Registro completado. Bienvenido a Legendario Manager.');
    }

    private function generateUniqueRut(): string
    {
        do {
            $rut = random_int(10_000_000, 99_999_999).'-'.random_int(0, 9);
        } while (User::query()->where('rut', $rut)->exists());

        return $rut;
    }
}
