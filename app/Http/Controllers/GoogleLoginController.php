<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleLoginController extends Controller
{
    /**
     * Redirect pengguna ke halaman autentikasi Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl($this->getRedirectUrl())
            ->redirect();
    }

    /**
     * Menangani callback dari Google OAuth.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->getRedirectUrl())
                ->stateless()
                ->user();
        } catch (Throwable $e) {
            Log::error('Google OAuth login failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal melakukan login dengan Google: '.$e->getMessage(),
            ]);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Jika user sudah terdaftar dan belum memiliki google_id, tautkan google_id
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }
        } else {
            // Jika user belum terdaftar, buat akun baru dengan data dari Google
            $user = User::create([
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'User Google'),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Str::random(32),
                'role' => 'staff',
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->to($this->dashboardUntukRole($user));
    }

    /**
     * Tentukan URL dashboard sesuai role pengguna.
     */
    protected function dashboardUntukRole(User $user): string
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return route('dashboard.admin');
        }

        if (method_exists($user, 'isStaff') && $user->isStaff()) {
            return route('dashboard.staff');
        }

        if (method_exists($user, 'isPimpinan') && $user->isPimpinan()) {
            return route('dashboard.pimpinan');
        }

        return route('dashboard.admin');
    }

    /**
     * Tentukan callback URI yang sesuai dengan host yang sedang dibuka oleh user (localhost vs 127.0.0.1).
     */
    protected function getRedirectUrl(): string
    {
        return route('auth.google.callback');
    }
}
