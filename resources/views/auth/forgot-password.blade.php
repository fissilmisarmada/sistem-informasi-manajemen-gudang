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
        .notice { padding:10px 12px; margin-bottom:14px; border-radius:8px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; font-size:12px; }
        .error { padding:10px 12px; margin-bottom:14px; border-radius:8px; background:#fff0f0; border:1px solid #f5b8b8; color:#a12626; font-size:12px; }
        label { display:block; margin-bottom:6px; color:#334e70; font-size:11px; font-weight:800; }
        input { width:100%; height:43px; padding:0 12px; border:1px solid #d5dee9; border-radius:8px; outline:0; color:#526b87; font-size:12px; box-sizing:border-box; }
        input:focus { border-color:#2563a6; box-shadow:0 0 0 3px #dbeafe; }
        button { width:100%; height:42px; margin-top:16px; border:0; border-radius:7px; background:#07539a; color:#fff; font-size:12px; font-weight:800; cursor:pointer; }
        .back { display:block; margin-top:17px; color:#15599b; text-align:center; font-size:11px; font-weight:700; }
    </style>

    <main class="auth-screen">
        <section class="auth-card">
            <h1>Lupa Password?</h1>
            <p>Masukkan email akun Anda. Kami akan mengirimkan tautan untuk membuat password baru.</p>

            @if(session('status'))
                <div class="notice">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="error">{{ $errors->first('email') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email">Email Akun</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                <button type="submit">Kirim Tautan Reset</button>
            </form>
            <a class="back" href="{{ route('login') }}">&larr; Kembali ke Login</a>
        </section>
    </main>
@endsection
