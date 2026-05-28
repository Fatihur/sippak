<?php

namespace App\Http\Controllers;

use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly LogAktivitasService $logAktivitasService,
    ) {}

    public function tampilLogin(): View
    {
        return view('auth.login');
    }

    public function redirectSetelahLogin(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $ingat = $request->boolean('remember');

        if (Auth::attempt($credentials + ['aktif' => true], $ingat)) {
            $request->session()->regenerate();
            $request->user()->update(['terakhir_login_at' => now()]);
            $this->logAktivitasService->catat('login', 'Petugas masuk ke sistem.');

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Berhasil masuk.');
        }

        return back()->withErrors(['email' => 'Email atau password tidak sesuai, atau akun tidak aktif.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->logAktivitasService->catat('logout', 'Petugas keluar dari sistem.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }

    public function tampilLupaPassword(): View
    {
        return view('auth.lupa-password');
    }

    public function kirimResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Link reset password sudah dikirim ke email petugas. Silakan cek inbox/spam.');
        }

        if (app()->environment(['local', 'testing'])) {
            $user = config('auth.providers.users.model')::where('email', $request->email)->first();
            if ($user) {
                $token = Password::createToken($user);

                return back()
                    ->with('success', 'Mode lokal: email reset belum terkirim melalui SMTP. Gunakan link demo di bawah ini untuk reset password.')
                    ->with('reset_link_demo', route('password.reset', ['token' => $token, 'email' => $user->email]));
            }
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function tampilResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.')
            : back()->withErrors(['email' => __($status)]);
    }
}
