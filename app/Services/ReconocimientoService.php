<?php

namespace App\Services;

use App\Mail\ReconocimientoMessageMail;
use App\Models\Reconocimiento;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReconocimientoService
{
    /**
     * @param  array<int>  $userIds
     * @param  array<string>  $channels
     * @return array<int, array{name: string, phone: ?string, url: ?string}>
     */
    public function sendToUsers(
        string $title,
        string $description,
        array $userIds,
        array $channels,
        string $motivo,
        ?string $imagePath,
        int $createdBy,
    ): array {
        $users = User::query()
            ->whereIn('id', $userIds)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        $whatsappLinks = [];

        foreach ($users as $user) {
            $reconocimiento = Reconocimiento::query()->create([
                'user_id' => $user->id,
                'nombre_reconocimiento' => $title,
                'descripcion' => $description,
                'motivo' => $motivo,
                'numero_legendario' => $user->numero_legendario ?: (string) $user->id,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'imagen_path' => $imagePath,
                'enviar_email' => in_array('email', $channels, true),
                'enviar_whatsapp' => in_array('whatsapp', $channels, true),
                'creado_por' => $createdBy,
            ]);

            if ($reconocimiento->enviar_email && filled($user->email)) {
                Mail::to($user->email)->send(new ReconocimientoMessageMail($reconocimiento));
                $reconocimiento->forceFill(['email_enviado_at' => now()])->save();
            }

            if ($reconocimiento->enviar_whatsapp) {
                $reconocimiento->forceFill(['whatsapp_preparado_at' => now()])->save();
                $whatsappLinks[] = [
                    'name' => $user->name,
                    'phone' => $user->telefono,
                    'url' => $this->whatsappUrl($user->telefono, $this->messageFor($reconocimiento)),
                ];
            }
        }

        return $whatsappLinks;
    }

    /**
     * @return Collection<int, User>
     */
    public function birthdayUsers(int $daysAhead = 30): Collection
    {
        return User::query()
            ->where('estado', 'activo')
            ->whereNotNull('fecha_nacimiento')
            ->get()
            ->map(function (User $user) {
                $birthday = $user->fecha_nacimiento->setYear((int) now()->format('Y'));

                if ($birthday->isBefore(now()->startOfDay())) {
                    $birthday = $birthday->addYear();
                }

                $user->next_birthday = $birthday;
                $user->days_until_birthday = now()->startOfDay()->diffInDays($birthday->startOfDay());

                return $user;
            })
            ->filter(fn (User $user) => $user->days_until_birthday <= $daysAhead)
            ->sortBy([
                ['days_until_birthday', 'asc'],
                ['apellido', 'asc'],
                ['nombre', 'asc'],
            ])
            ->values();
    }

    public function storeImage(mixed $file): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store('reconocimientos', 'public');
    }

    public function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    public function defaultBirthdayMessage(): string
    {
        return 'Que tengas un dia lleno de alegria, gratitud y bendicion. Gracias por ser parte de Legendarios.';
    }

    private function messageFor(Reconocimiento $reconocimiento): string
    {
        $name = trim(($reconocimiento->nombre ?? '').' '.($reconocimiento->apellido ?? ''));

        return trim($reconocimiento->nombre_reconocimiento."\n\n".($name !== '' ? $name."\n\n" : '').($reconocimiento->descripcion ?? ''));
    }

    private function whatsappUrl(?string $phone, string $message): ?string
    {
        $normalized = $this->normalizePhone($phone);

        if ($normalized === null) {
            return null;
        }

        return 'https://wa.me/'.$normalized.'?text='.rawurlencode($message);
    }

    private function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
        }

        if (strlen($digits) === 9) {
            $digits = '56'.$digits;
        }

        return $digits;
    }
}
