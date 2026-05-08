<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReconocimientoRequest;
use App\Models\Reconocimiento;
use App\Models\User;
use App\Services\ReconocimientoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReconocimientoController extends Controller
{
    public function __construct(
        private readonly ReconocimientoService $service,
    ) {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $reconocimientos = Reconocimiento::query()
            ->with(['creadoPor', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query
                    ->where('nombre_reconocimiento', 'like', "%{$q}%")
                    ->orWhere('numero_legendario', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('apellido', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reconocimientos.index', [
            'reconocimientos' => $reconocimientos,
            'q' => $q,
            'birthdayUsers' => $this->service->birthdayUsers(),
            'birthdayTitle' => 'Feliz cumpleanos',
            'birthdayMessage' => $this->service->defaultBirthdayMessage(),
            'whatsappLinks' => session('whatsapp_links', []),
        ]);
    }

    public function create(): View
    {
        $users = User::query()
            ->where('estado', 'activo')
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get(['id', 'numero_legendario', 'nombre', 'apellido', 'email', 'telefono']);

        return view('admin.reconocimientos.form', [
            'users' => $users,
        ]);
    }

    public function store(StoreReconocimientoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $whatsappLinks = $this->service->sendToUsers(
            title: $data['titulo'],
            description: $data['descripcion'],
            userIds: $data['user_ids'],
            channels: $data['channels'],
            motivo: 'personalizado',
            imagePath: $this->service->storeImage($request->file('imagen')),
            createdBy: (int) auth()->id(),
        );

        return redirect()
            ->route('admin.reconocimientos.index')
            ->with('success', 'Reconocimiento enviado correctamente.')
            ->with('whatsapp_links', $whatsappLinks);
    }

    public function sendBirthdays(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->hasAnyRole(['Administrador', 'Supervisor']), 403);

        $data = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['string', 'in:email,whatsapp'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:2000'],
        ]);

        $birthdayIds = $this->service->birthdayUsers()
            ->where('days_until_birthday', 0)
            ->pluck('id')
            ->all();

        $selectedIds = array_values(array_intersect($data['user_ids'], $birthdayIds));

        if ($selectedIds === []) {
            return redirect()->route('admin.reconocimientos.index')->with('warning', 'Selecciona al menos un cumpleanero de hoy.');
        }

        $whatsappLinks = $this->service->sendToUsers(
            title: $data['titulo'],
            description: $data['descripcion'],
            userIds: $selectedIds,
            channels: $data['channels'],
            motivo: 'cumpleanos',
            imagePath: null,
            createdBy: (int) auth()->id(),
        );

        return redirect()
            ->route('admin.reconocimientos.index')
            ->with('success', 'Felicitaciones de cumpleanos enviadas correctamente.')
            ->with('whatsapp_links', $whatsappLinks);
    }

    public function destroy(Reconocimiento $reconocimiento): RedirectResponse
    {
        abort_unless(auth()->user()?->hasAnyRole(['Administrador', 'Supervisor']), 403);

        $this->service->deleteImage($reconocimiento->imagen_path);

        $reconocimiento->delete();

        return redirect()->route('admin.reconocimientos.index')->with('success', 'Reconocimiento eliminado correctamente.');
    }
}
