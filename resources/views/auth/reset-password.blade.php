@extends('layouts.app')

@section('content')
    <style>
        body { background:#f4f8fd !important; }
        .navbar { display:none !important; }
        .container { max-width:none !important; padding:0 !important; }
        .auth-screen { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px 16px; }
        .auth-card { width:min(420px,100%); padding:28px 22px 24px; background:#fff; border-radius:16px; box-shadow:0 10px 28px rgba(31,70,112,.10); }
        .auth-card h1 { margin:0; text-align:center; color:#173b68; font-size:22px; }
        .auth-card p { margin:8px 0 22px; color:#70829a; text-align:center; font-size:12px; line-height:1.5; }
        .error { padding:10px 12px; margin-bottom:14px; border-radius:8px; background:#fff0f0; border:1px solid #f5b8b8; color:#a12626; font-size:12px; }
        .field { margin-bottom:14px; }
        label { display:block; margin-bottom:6px; color:#334e70; font-size:11px; font-weight:800; }
        input { width:100%; height:43px; padding:0 12px; border:1px solid #d5dee9; border-radius:8px; outline:0; color:#526b87; font-size:12px; box-sizing:border-box; }
        input:focus { border-color:#2563a6; box-shadow:0 0 0 3px #dbeafe; }
        button { width:100%; height:42px; margin-top:4px; border:0; border-radius:7px; background:#07539a; color:#fff; font-size:12px; font-weight:800; cursor:pointer; }
        .back { display:block; margin-top:17px; color:#15599b; text-align:center; font-size:11px; font-weight:700; }
    </style>

    <main class="auth-screen">
        <section class="auth-card">
            <h1>Buat Password Baru</h1>
            <p>Gunakan password baru minimal 8 karakter untuk mengamankan akun Anda.</p>

            @if($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="field"><label for="email">Email Akun</label><input id="email" name="email" type="email" value="{{ old('email', request('email')) }}" required autofocus></div>
                <div class="field"><label for="password">Password Baru</label><input id="password" name="password" type="password" minlength="8" required></div>
                <div class="field"><label for="password_confirmation">Konfirmasi Password Baru</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required></div>
                <button type="submit">Simpan Password Baru</button>
            </form>
            <a class="back" href="{{ route('login') }}">&larr; Kembali ke Login</a>
        </section>
    </main>
@endsection
