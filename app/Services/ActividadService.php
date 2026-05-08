<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\Asistencia;
use App\Models\Departamento;
use App\Models\DepartamentoActividad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActividadService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createGlobal(array $data, ?int $createdByUserId = null): Actividad
    {
        return DB::transaction(function () use ($data, $createdByUserId) {
            $rawToken = Str::random(64);

            $actividad = Actividad::query()->create([
                'nombre' => $data['nombre'],
                'fecha' => $data['fecha'],
                'precio' => (int) ($data['precio'] ?? 0),
                'registration_token_hash' => hash('sha256', $rawToken),
                'raw_registration_token' => $rawToken,
                'registration_expires_at' => null,
                'estado' => 'abierta',
                'cerrada_en' => null,
                'cerrada_por' => null,
            ]);

            $departamentos = Departamento::query()->get(['id']);

            foreach ($departamentos as $departamento) {
                DepartamentoActividad::query()->create([
                    'departamento_id' => $departamento->id,
                    'actividad_id' => $actividad->id,
                    'estado' => 'abierta',
                    'pago_estado' => 'pagado',
                    'pago_monto_total' => 0,
                    'pago_monto_pagado' => 0,
                ]);
            }

            return $actividad;
        });
    }

    public function closeActividad(Actividad $actividad, int $closedByUserId): void
    {
        DB::transaction(function () use ($actividad, $closedByUserId) {
            $actividad->forceFill([
                'estado' => 'cerrada',
                'cerrada_en' => now(),
                'cerrada_por' => $closedByUserId,
            ])->save();

            DepartamentoActividad::query()
                ->where('actividad_id', $actividad->id)
                ->update([
                    'estado' => 'cerrada',
                    'updated_at' => now(),
                ]);

            // Asistencias ya se crean como 'ausente'.
            // Aseguramos que cualquier registro no marcado quede 'ausente'.
            Asistencia::query()
                ->whereIn('departamento_actividad_id', function ($q) use ($actividad) {
                    $q->select('id')->from('departamento_actividades')->where('actividad_id', $actividad->id);
                })
                ->whereNull('marcada_en')
                ->update([
                    'estado' => 'ausente',
                    'updated_at' => now(),
                ]);
        });
    }
}
