<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * TAHAP 1 (sequence diagram "Login" pesan 1-2):
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        $users = User::query()
            ->select(['name', 'email', 'role'])
            ->orderBy('name')
            ->get();

        return view('auth.login', compact('users'));
    }

    /**
     * TAHAP 2 (sequence diagram "Login" pesan 3-13):
     * Memproses input email & password, lalu mengarahkan pengguna
     * ke dashboard sesuai role -- atau menampilkan pesan error
     * kalau kredensialnya tidak valid.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Auth::attempt() ini yang mewakili method User::findByEmail() +
        // pengecekan password di sequence diagram (pesan 5-8) sekaligus --
        // Laravel otomatis cari user berdasarkan email, lalu cocokkan
        // password yang diinput dengan password ter-hash di database.
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- Kondisi [email & password valid] ---
            // Arahkan ke dashboard sesuai role (pesan 9-11)
            return redirect()->intended($this->dashboardUntukRole());
        }

        // --- Kondisi [kredensial tidak valid] ---
        // (pesan 12-13)
        return back()
            ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])
            ->onlyInput('email');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($credentials);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan reset password telah dikirim ke email Anda.');
        }

        return back()->withErrors(['email' => 'Email tersebut tidak terdaftar.']);
    }

    public function showResetPasswordForm(string $token)
    {
        return view('auth.reset-password', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        $credentials = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $credentials,
            function (User $user, string $password) {
                $user->password = $password;
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan masuk dengan password baru.');
        }

        return back()->withErrors(['email' => 'Tautan reset password tidak valid atau sudah kedaluwarsa.']);
    }

    /**
     * TAHAP (sequence diagram "Logout" pesan 1-5):
     * Mengakhiri sesi pengguna dan mengarahkan kembali ke halaman login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus & bikin ulang session, supaya data sesi lama
        // gak bisa dipakai lagi setelah logout (praktik keamanan standar).
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect user to appropriate dashboard or login page.
     */
    public function dashboardRedirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return redirect()->intended($this->dashboardUntukRole());
    }

    /**
     * Method bantu: tentukan halaman dashboard tujuan berdasarkan role
     * user yang baru saja login. Dipanggil dari login() di atas.
     */
    protected function dashboardUntukRole(): string
    {
        $user = Auth::user();

        // Jika tidak ada user (shouldn't happen right after successful login,
        // tapi aman untuk menangani kasus ini) kembalikan ke dashboard admin.
        if (!$user) {
            return route('dashboard.admin');
        }

        // Periksa method role dengan aman sebelum pemanggilan.
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
}
