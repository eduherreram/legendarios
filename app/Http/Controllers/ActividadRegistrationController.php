<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadCompromiso;
use App\Models\Asistencia;
use App\Models\DepartamentoActividad;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ActividadRegistrationController extends Controller
{
    public function create(string $token): View
    {
        $actividad = $this->findActividad($token);

        return view('registro.actividad', [
            'actividad' => $actividad,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $actividad = $this->findActividad($token);

        $data = $request->validate([
            'numero_legendario' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()
            ->where('estado', 'activo')
            ->where('numero_legendario', $data['numero_legendario'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'numero_legendario' => ['No encontramos un servidor activo con ese numero legendario.'],
            ]);
        }

        if (! $user->departamento_id) {
            throw ValidationException::withMessages([
                'numero_legendario' => ['Este servidor no tiene departamento asignado.'],
            ]);
        }

        DB::transaction(function () use ($actividad, $user) {
            $departamentoActividad = DepartamentoActividad::query()
                ->where('actividad_id', $actividad->id)
                ->where('departamento_id', $user->departamento_id)
                ->firstOrFail();

            $compromiso = ActividadCompromiso::query()->firstOrCreate(
                [
                    'actividad_id' => $actividad->id,
                    'user_id' => $user->id,
                ],
                [
                    'departamento_actividad_id' => $departamentoActividad->id,
                    'registered_at' => now(),
                ],
            );

            Asistencia::query()->firstOrCreate(
                [
                    'departamento_actividad_id' => $departamentoActividad->id,
                    'user_id' => $user->id,
                ],
                [
                    'actividad_compromiso_id' => $compromiso->id,
                    'estado' => 'ausente',
                    'pago_estado' => 'pendiente',
                    'pago_monto_pagado' => 0,
                    'marcada_en' => null,
                    'marcada_por' => null,
                ],
            );

            $this->syncDepartamentoPago($departamentoActividad);
        });

        return redirect()->route('actividad.register.success')->with('success', 'Compromiso registrado correctamente.');
    }

    public function success(): View
    {
        return view('registro.actividad-success');
    }

    private function findActividad(string $token): Actividad
    {
        $actividad = Actividad::query()
            ->where('registration_token_hash', hash('sha256', $token))
            ->firstOrFail();

        if ($actividad->estado === 'cerrada') {
            abort(410);
        }

        if ($actividad->registration_expires_at !== null && $actividad->registration_expires_at->isPast()) {
            abort(410);
        }

        return $actividad;
    }

    private function syncDepartamentoPago(DepartamentoActividad $departamentoActividad): void
    {
        $precio = (int) ($departamentoActividad->actividad?->precio ?? 0);
        $asistencias = Asistencia::query()
            ->where('departamento_actividad_id', $departamentoActividad->id)
            ->get(['pago_estado', 'pago_monto_pagado']);

        $total = $precio * $asistencias->reject(fn ($a) => $a->pago_estado === 'freepass')->count();
        $pagado = (int) $asistencias->sum('pago_monto_pagado');
        $estado = 'pendiente';

        if ($total <= 0 || $pagado >= $total) {
            $estado = 'pagado';
        } elseif ($pagado > 0 || $asistencias->contains(fn ($a) => in_array($a->pago_estado, ['abonado', 'freepass'], true))) {
            $estado = 'pagado_parcial';
        }

        $departamentoActividad->forceFill([
            'pago_estado' => $estado,
            'pago_monto_total' => $total,
            'pago_monto_pagado' => $pagado,
        ])->save();
    }
}
