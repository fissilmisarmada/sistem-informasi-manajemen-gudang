@extends('layouts.app')

@section('content')
    <style>
        body { background:#f4f8fd !important; }
        .navbar { display:none !important; }
        .container { max-width:none !important; padding:0 !important; }
        .login-screen { min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:30px 20px 18px; color:#173b68; overflow:hidden; }
        .login-brand { text-align:center; }
        .university-mark { width:min(118px,30vw); height:auto; margin:0 auto 12px; display:block; }
        .university-mark img { display:block; width:100%; height:auto; }
        .login-brand strong { display:block; color:#07539a; font-size:9px; letter-spacing:.1px; }
        .login-brand h1 { margin:15px 0 3px; color:#153d70; font-size:24px; letter-spacing:.4px; }
        .login-brand p { margin:0; color:#526b87; font-size:11px; line-height:1.45; }
        .warehouse-scene { width:min(430px,90vw); height:130px; position:relative; margin:13px auto -1px; }
        .warehouse-building { position:absolute; right:40px; bottom:0; width:170px; height:92px; background:#dcebf8; opacity:.85; clip-path:polygon(50% 0,100% 27%,100% 100%,0 100%,0 27%); }
        .warehouse-building:after { content:''; position:absolute; left:61px; bottom:0; width:48px; height:53px; background:#bad2e8; border-radius:24px 24px 0 0; }
        .boxes { position:absolute; right:70px; bottom:0; width:85px; height:54px; background:linear-gradient(90deg,#c9985b 0 32%,transparent 32% 36%,#d6a76b 36% 68%,transparent 68% 72%,#bd8c51 72%); border:2px solid #b98145; opacity:.82; }
        .shelf { position:absolute; left:27px; bottom:0; width:105px; height:105px; border-left:7px solid #477ca6; border-right:7px solid #477ca6; background:repeating-linear-gradient(to bottom,transparent 0 26px,#477ca6 26px 32px); }
        .shelf:before { content:''; position:absolute; left:12px; top:7px; width:20px; height:88px; background:repeating-linear-gradient(to bottom,#c9945a 0 18px,#f0c78b 18px 22px); box-shadow:30px 0 #6c9cc1,58px 0 #d6a76b; opacity:.85; }
        .login-card { width:min(430px,100%); margin-top:-1px; padding:25px 17px 18px; background:#fff; border-radius:18px; box-shadow:0 10px 28px rgba(31,70,112,.10); }
        .login-card h2 { margin:0; text-align:center; color:#173b68; font-size:18px; }
        .login-card .intro { margin:6px 0 20px; text-align:center; color:#70829a; font-size:11px; }
        .login-error { background:#fff0f0; border:1px solid #f5b8b8; color:#a12626; padding:10px 12px; border-radius:8px; margin-bottom:14px; font-size:12px; }
        .login-error ul { margin:0; padding-left:17px; }
        .login-field { margin-bottom:14px; }
        .login-field label { display:block; margin-bottom:6px; color:#334e70; font-size:11px; font-weight:800; }
        .field-shell { display:flex; align-items:center; gap:9px; height:43px; padding:0 12px; border:1px solid #d5dee9; border-radius:8px; background:#fff; }
        .field-shell:focus-within { border-color:#2563a6; box-shadow:0 0 0 3px #dbeafe; }
        .field-icon { color:#94a3b8; font-size:17px; }
        .field-shell select, .field-shell input { width:100%; height:100%; border:0; outline:0; background:transparent; color:#526b87; font-size:12px; }
        .password-toggle { border:0; background:transparent; color:#8494a8; cursor:pointer; padding:0; font-size:16px; }
        .forgot { display:block; margin:-3px 0 14px; text-align:right; color:#15599b; font-size:11px; font-weight:700; }
        .login-submit, .pimpinan-button { width:100%; height:42px; border-radius:7px; font-size:12px; font-weight:800; cursor:pointer; }
        .login-submit { border:0; background:#07539a; color:#fff; box-shadow:0 5px 10px rgba(7,83,154,.18); }
        .login-submit:hover { background:#06447e; }
        .divider { display:flex; align-items:center; gap:12px; margin:15px 0; color:#8190a3; font-size:11px; }
        .divider:before, .divider:after { content:''; flex:1; height:1px; background:#e2e8f0; }
        .pimpinan-button { border:1px solid #9fc3e5; background:#fff; color:#07539a; }
        .pimpinan-button:hover { background:#eff6ff; }
        .login-footer { margin-top:14px; color:#8494a8; text-align:center; font-size:10px; }
        @media (max-height:760px) { .login-screen { padding-top:15px; } .warehouse-scene { height:105px; transform:scale(.82); margin:-6px auto -12px; } .login-card { padding-top:20px; } }
    </style>

    <main class="login-screen">
        <header class="login-brand">
            <div class="university-mark"><img src="{{ asset('images/Logo_Universitas_Terbuka.svg') }}" alt="Logo Universitas Terbuka"></div>
            <h1>MASIH BELUM DPT NAMA</h1>
            <p>Sistem Informasi Manajemen Gudang<br>Universitas Terbuka</p>
        </header>

        <div class="warehouse-scene" aria-hidden="true"><div class="shelf"></div><div class="warehouse-building"></div><div class="boxes"></div></div>

        <section class="login-card">
            <h2>Masuk ke Akun Anda</h2>
            <p class="intro">Silakan masuk menggunakan akun yang terdaftar</p>

            @if(session('status'))
                <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;padding:10px 12px;border-radius:8px;margin-bottom:14px;font-size:12px;">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="login-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ route('login.perform') }}">
                @csrf
                <div class="login-field">
                    <label for="email">Email</label>
                    <div class="field-shell"><span class="field-icon">&#9993;</span><select id="email" name="email" required><option value="">Masukkan email Anda</option>@foreach($users as $user)<option value="{{ $user->email }}" @selected(old('email') === $user->email)>{{ $user->email }}</option>@endforeach</select></div>
                </div>
                <div class="login-field">
                    <label for="password">Password</label>
                    <div class="field-shell"><span class="field-icon">&#128274;</span><input type="password" id="password" name="password" placeholder="Masukkan password Anda" required><button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password">&#128065;</button></div>
                </div>
                <a class="forgot" href="{{ route('password.request') }}">Lupa password?</a>
                <button class="login-submit" type="submit">Masuk</button>
            </form>

            <div class="divider">atau</div>
            <button class="pimpinan-button" type="button" id="pimpinanLogin">&#9822; &nbsp; Masuk sebagai Pimpinan</button>
        </section>
        <footer class="login-footer">&copy; 2026 Fissilmi & Ismail. All rights reserved.</footer>
    </main>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            password.type = password.type === 'password' ? 'text' : 'password';
        });
        document.getElementById('pimpinanLogin').addEventListener('click', function () {
            const email = document.getElementById('email');
            const option = Array.from(email.options).find((item) => item.value.includes('pimpinan'));
            if (option) email.value = option.value;
            document.getElementById('password').focus();
        });
    </script>
@endsection
