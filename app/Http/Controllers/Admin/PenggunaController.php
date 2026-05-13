<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function __construct(
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function index(): View
    {
        return view('admin.pengguna.index', ['pengguna' => User::latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.pengguna.form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:operator,kepala_bidang,kepala_dinas'],
            'aktif' => ['nullable', 'boolean'],
        ]);
        $data['aktif'] = $request->boolean('aktif');
        $user = User::create($data);
        $this->logAktivitasService->catat('pengguna_ditambahkan', 'Email: '.$user->email);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna): View
    {
        return view('admin.pengguna.form', ['user' => $pengguna]);
    }

    public function update(Request $request, User $pengguna): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$pengguna->id],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'role' => ['required', 'in:operator,kepala_bidang,kepala_dinas'],
            'aktif' => ['nullable', 'boolean'],
        ]);
        $data['aktif'] = $request->boolean('aktif');
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        $pengguna->update($data);
        $this->logAktivitasService->catat('pengguna_diperbarui', 'Email: '.$pengguna->email);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna): RedirectResponse
    {
        abort_if(auth()->id() === $pengguna->id, 422, 'Tidak dapat menonaktifkan akun sendiri.');
        $pengguna->update(['aktif' => false]);
        $this->logAktivitasService->catat('pengguna_dinonaktifkan', 'Email: '.$pengguna->email);

        return back()->with('success', 'Pengguna berhasil dinonaktifkan.');
    }
}
