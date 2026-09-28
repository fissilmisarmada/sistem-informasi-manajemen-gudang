@extends('layouts.app')

@section('content')
<style>
    body { background: var(--andon-bg); overflow: hidden; }
    .site-header { display: none !important; }
    .site-main { max-width: none !important; width: 100%; margin-left: 0 !important; padding: 0 !important; }

    .andon-login {
        min-height: 100dvh;
        display: grid;
        place-items: center;
        padding: 16px;
        position: relative;
        overflow: hidden;
        background: url('{{ asset('images/UT1.jpg') }}') center/cover no-repeat;
    }
    .andon-login::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: repeating-linear-gradient(90deg, var(--andon-amber) 0 14px, transparent 14px 28px);
        z-index: 2; pointer-events: none;
    }
    .andon-login::after {
        content: '';
        position: absolute; inset: 0;
        background:
            radial-gradient(900px 420px at 18% 12%, rgba(239,131,84,.14) 0%, transparent 62%),
            linear-gradient(180deg, rgba(253,251,246,.74) 0%, rgba(246,241,232,.82) 100%);
        pointer-events: none;
    }
    .andon-login__card {
        width: 100%;
        max-width: 360px;
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(226,232,240,.9);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(15,23,42,.12);
        overflow: hidden;
        position: relative;
        z-index: 1;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .andon-login__card::before {
        content: ''; position: absolute; left: 0; right: 0; top: 0; height: 3px;
        background: var(--andon-amber);
    }
    .andon-login__inner { padding: 20px 18px 16px; }
    .andon-login__brand {
        text-align: center;
        margin-bottom: 12px;
    }
    .andon-login__brand img {
        height: 48px; width: auto; object-fit: contain;
        display: block; margin: 0 auto 8px;
    }
    .andon-login__brand h1 {
        margin: 0; font-size: 18px; font-weight: 800; letter-spacing: -.03em; color: var(--andon-ink);
    }
    .andon-login__brand p {
        margin: 3px 0 0; font-size: 11px; color: var(--andon-muted); line-height: 1.4;
    }
    .andon-kicker--login { justify-content: center; margin-bottom: 10px; }
    .andon-alert { padding: 10px 14px; border-radius: 12px; font-size: 12px; margin-bottom: 14px; font-weight: 600; line-height: 1.4; }
    .andon-alert--ok { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; }
    .andon-alert--err { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
    .andon-alert--err ul { margin: 6px 0 0; padding-left: 18px; }
    .andon-field { margin-bottom: 14px; }
    .andon-field label { display: block; font-size: 10px; font-weight: 800; letter-spacing: .12em; color: var(--andon-faint); margin-bottom: 6px; }
    .andon-field__head { display:flex; align-items:baseline; justify-content:space-between; gap:12px; margin-bottom:6px; }
    .andon-field__head label { margin-bottom:0; }
    .andon-forgot--inline { display:inline; margin:0; font-size:11px; font-weight:700; color:var(--andon-navy); white-space:nowrap; }
    .andon-forgot--inline:hover { text-decoration:underline; }

    .andon-input {
        position: relative; display: flex; align-items: center;
    }
    .andon-input__icon {
        position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px; color: var(--andon-faint); pointer-events: none; z-index: 2;
    }
    .andon-control {
        width: 100%; box-sizing: border-box;
        height: 46px; line-height: 20px;
        padding-left: 22px !important; /* Memberikan ruang ekstra dari ikon */
        padding-right: 16px;
        font-size: 13px; font-weight: 600;
        color: var(--andon-ink);
        background: #FBFBFD;
        border: 1px solid #E8EAF0;
        border-radius: 14px;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        font-family: inherit;
        appearance: none; -webkit-appearance: none;
    }
    .andon-control:focus {
        outline: none;
        background: #fff;
        border-color: var(--andon-ink);
        box-shadow: 0 0 0 3px rgba(15,23,42,.06);
    }

    /* KHUSUS UNTUK DROPDOWN SELECT */
    .andon-control--select {
        padding-right: 16px !important;
        cursor: pointer;
        /* Trik khusus browser agar teks pilihan tidak menempel pada ikon */
        text-indent: 1px;
        text-overflow: '';
    }

    .andon-toggle {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        width: 32px; height: 32px; border-radius: 8px;
        border: 1px solid transparent; background: transparent; color: var(--andon-faint);
        display: grid; place-items: center; cursor: pointer; z-index: 2;
    }
    .andon-toggle:hover { background: #F1F5F9; color: var(--andon-ink); border-color: var(--andon-line); }
    .andon-toggle svg { width: 16px; height: 16px; }
    .andon-forgot {
        display: block; text-align: right; margin: -2px 0 25px;
        font-size: 11px; font-weight: 700; color: var(--andon-navy);
    }
    .andon-forgot:hover { text-decoration: underline; }
    .andon-btn {
        width: 100%; min-height: 42px; padding: 0 16px;
        border-radius: 999px; border: 1px solid transparent;
        font-size: 13px; font-weight: 800; letter-spacing: -.01em;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        cursor: pointer; transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
        font-family: inherit;
    }
    .andon-btn--primary {
        background: var(--andon-ink); color: #fff; border-color: var(--andon-ink);
        box-shadow: 0 8px 20px rgba(15,23,42,.18);
    }
    .andon-btn--primary:hover { background: var(--andon-navy-2); transform: translateY(-1px); }
    .andon-btn--ghost {
        background: #fff; color: var(--andon-ink); border-color: var(--andon-line-strong);
    }
    .andon-btn--ghost:hover { border-color: var(--andon-ink); transform: translateY(-1px); box-shadow: var(--andon-shadow); }
    .andon-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 16px 0; color: var(--andon-faint); font-size: 10px; font-weight: 800; letter-spacing: .12em;
    }
    .andon-divider::before, .andon-divider::after { content: ''; flex: 1; height: 1px; background: var(--andon-line); }
    .andon-foot { text-align: center; margin-top: 14px; font-size: 10px; font-weight: 600; letter-spacing: .06em; color: var(--andon-faint); }
</style>

<div class="andon-login">
    <section class="andon-login__card">
        <div class="andon-login__inner">
            <header class="andon-login__brand">
                <span class="andon-kicker andon-kicker--login" style="color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.14em;display:inline-flex;align-items:center;gap:8px;"><i style="width:18px;height:2px;background:var(--andon-amber);display:inline-block;border-radius:999px;"></i> MASUK SISTEM GUDANG</span>
                <img src="{{ asset('images/Logo_Universitas_Terbuka.svg') }}" alt="Logo Universitas Terbuka">
                <h1>MAGS-UT</h1>
                <p>Manajemen Gudang & Stok<br>Universitas Terbuka</p>
            </header>

            @if(session('status'))
                <div class="andon-alert andon-alert--ok">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="andon-alert andon-alert--err">
                    Terjadi kesalahan:
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.perform') }}">
                @csrf
                <div class="andon-field">
                    <div class="andon-field__head">
                        <label for="email">EMAIL ADDRESS</label>
                        <a class="andon-forgot--inline" href="{{ route('password.request') }}">Lupa password?</a>
                    </div>
                    <div class="andon-input">
                        <svg class="andon-input__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8l8 5 8-5"/><rect x="3" y="7" width="18" height="12" rx="2"/></svg>
                        <select id="email" name="email" class="andon-control andon-control--select" required>
                            <option value="" disabled selected hidden>Pilih email Anda</option>
                            @foreach($users as $user)
                                <option value="{{ $user->email }}" @selected(old('email') === $user->email)>{{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="andon-field">
                    <label for="password">PASSWORD</label>
                    <div class="andon-input">
                        <svg class="andon-input__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M9 11V8a3 3 0 016 0v3"/></svg>
                        <input type="password" id="password" name="password" class="andon-control" style="padding-right:48px !important;" placeholder="Masukkan password" required>
                        <button type="button" class="andon-toggle" id="togglePassword" aria-label="Tampilkan password">
                            <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eye-slash-icon" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 10.6A3 3 0 0012 15a3 3 0 002.4-4.4"/><path d="M9.9 5.1A11 11 0 0112 5c7 0 11 7 11 7a17 17 0 01-4.2 5.1"/><path d="M14.8 14.8A11 11 0 0012 19c-7 0-11-7-11-7a17 17 0 014.2-5.1"/></svg>
                        </button>
                    </div>
                </div>

                <button class="andon-btn andon-btn--primary" type="submit">Masuk</button>
            </form>

            <div class="andon-foot">© 2026 Fissilmi & Ismail · Sistem Gudang UT</div>
        </div>
    </section>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pw = document.getElementById('password');
        const eye = document.getElementById('eye-icon');
        const eyeSlash = document.getElementById('eye-slash-icon');
        const isPw = pw.type === 'password';
        pw.type = isPw ? 'text' : 'password';
        eye.style.display = isPw ? 'none' : 'block';
        eyeSlash.style.display = isPw ? 'block' : 'none';
    });
</script>
@endsection
