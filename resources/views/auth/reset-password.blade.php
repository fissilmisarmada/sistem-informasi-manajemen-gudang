@extends('layouts.app')

@section('content')
<style>
    body { overflow: hidden; }
    .site-header { display: none !important; }
    .site-main { max-width: none !important; width: 100%; margin-left: 0 !important; padding: 0 !important; }
    .andon-auth {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 24px 16px;
        background:
            radial-gradient(900px 420px at 18% 12%, rgba(239,131,84,.16) 0%, transparent 62%),
            linear-gradient(180deg, #FDFBF6 0%, var(--andon-bg) 100%);
        position: relative;
    }
    .andon-auth::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: repeating-linear-gradient(90deg, var(--andon-amber) 0 14px, transparent 14px 28px);
    }
    .andon-auth__card {
        width: 100%; max-width: 420px;
        background: var(--andon-panel); border: 1px solid #EDEEF2; border-radius: 20px;
        box-shadow: var(--andon-shadow-strong); overflow: hidden; position: relative;
    }
    .andon-auth__card::before { content: ''; position: absolute; left: 0; right: 0; top: 0; height: 3px; background: var(--andon-amber); }
    .andon-auth__inner { padding: 26px 26px 20px; }
    .andon-auth__brand { text-align: center; margin-bottom: 18px; }
    .andon-auth__brand h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -.03em; color: var(--andon-ink); }
    .andon-auth__brand p { margin: 6px 0 0; color: var(--andon-muted); font-size: 12px; line-height: 1.5; }
    .andon-alert--err { padding: 10px 14px; border-radius: 12px; font-size: 12px; margin-bottom: 14px; font-weight: 600; background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
    .andon-field { margin-bottom: 14px; }
    .andon-field label { display: block; margin-bottom: 8px; color: var(--andon-muted); font-size: 11px; font-weight: 700; letter-spacing: .06em; }
    .andon-control {
        width: 100%; padding: 12px 14px; font-size: 13px; font-weight: 600; color: var(--andon-ink);
        background: #FBFBFD; border: 1px solid #E8EAF0; border-radius: 14px;
        outline: none; box-sizing: border-box; font-family: inherit;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .andon-control:focus { background: #fff; border-color: var(--andon-ink); box-shadow: 0 0 0 3px rgba(15,23,42,.06); }
    .andon-btn {
        width: 100%; min-height: 42px; padding: 0 16px; border-radius: 999px; border: 1px solid transparent;
        font-size: 13px; font-weight: 800; letter-spacing: -.01em;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        cursor: pointer; transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        font-family: inherit;
    }
    .andon-btn--primary { background: var(--andon-ink); color: #fff; border-color: var(--andon-ink); box-shadow: 0 8px 20px rgba(15,23,42,.18); }
    .andon-btn--primary:hover { background: var(--andon-navy-2); transform: translateY(-1px); }
    .andon-back { display: grid; place-items: center; width: 40px; height: 40px; margin: 14px auto 0; border-radius: 999px; background: #fff; color: var(--andon-ink); border: 1px solid var(--andon-line-strong); box-shadow: var(--andon-shadow); text-decoration: none; }
    .andon-back:hover { border-color: var(--andon-ink); transform: translateY(-1px); box-shadow: var(--andon-shadow-strong); }
    .andon-back svg { width: 16px; height: 16px; }
    .andon-foot { text-align: center; margin-top: 14px; font-size: 10px; font-weight: 600; letter-spacing: .06em; color: var(--andon-faint); }
</style>

<div class="andon-auth">
    <section class="andon-auth__card">
        <div class="andon-auth__inner">
            <header class="andon-auth__brand">
                <span class="andon-kicker" style="justify-content:center;color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.14em;display:inline-flex;align-items:center;gap:8px;margin-bottom:10px;"><i style="width:18px;height:2px;background:var(--andon-amber);display:inline-block;border-radius:999px;"></i> PEMULIHAN AKSES</span>
                <h1>Buat Password Baru</h1>
                <p>Gunakan password baru minimal 8 karakter untuk mengamankan akun Anda.</p>
            </header>

            @if($errors->any())
                <div class="andon-alert--err">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="andon-field"><label for="email">EMAIL AKUN</label><input class="andon-control" id="email" name="email" type="email" value="{{ old('email', request('email')) }}" required autofocus placeholder="email@contoh.id"></div>
                <div class="andon-field"><label for="password">PASSWORD BARU</label><input class="andon-control" id="password" name="password" type="password" minlength="8" required placeholder="••••••••"></div>
                <div class="andon-field"><label for="password_confirmation">KONFIRMASI PASSWORD BARU</label><input class="andon-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required placeholder="Ulangi password"></div>
                <button class="andon-btn andon-btn--primary" type="submit">Simpan Password Baru</button>
            </form>
            <a class="andon-back" href="{{ route('login') }}" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
            <div class="andon-foot">© 2026 Fissilmi & Ismail · Sistem Gudang UT</div>
        </div>
    </section>
</div>
@endsection
