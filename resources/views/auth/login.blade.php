@extends('layouts.app')

@section('content')
    <style>
        body { background:#1C396A !important; }
        .navbar { display:none !important; }
        .container { max-width:none !important; padding:0 !important; }
        .login-screen { min-height:100vh; position:relative; display:flex; flex-direction:column; align-items:center; padding:30px 20px 18px; color:#fff; overflow:hidden; background:url('/images/UT1.jpg') center/cover no-repeat; }
        .login-screen::before { content:''; position:absolute; inset:0; background:rgba(28,57,106,.58); }
        .login-screen > * { position:relative; z-index:1; }
        .login-brand { width:min(430px,100%); position:relative; padding:8px 20px 10px; text-align:center; text-shadow:0 2px 5px rgba(0,0,0,.7); }
        .login-brand > * { position:relative; z-index:1; }
        .university-mark { width:min(118px,30vw); height:auto; margin:0 auto 12px; display:block; }
        .university-mark img { display:block; width:100%; height:auto; }
        .login-brand strong { display:block; color:#1651A4; font-size:9px; letter-spacing:.1px; }
        .login-brand h1 { margin:15px 0 3px; color:#fff; font-size:24px; letter-spacing:.4px; }
        .login-brand p { margin:0; color:#fff; font-size:11px; line-height:1.45; }
        .warehouse-scene { display:none; width:min(430px,90vw); height:130px; position:relative; margin:13px auto -1px; }
        .warehouse-building { position:absolute; right:40px; bottom:0; width:170px; height:92px; background:#dbe7f5; opacity:.85; clip-path:polygon(50% 0,100% 27%,100% 100%,0 100%,0 27%); }
        .warehouse-building:after { content:''; position:absolute; left:61px; bottom:0; width:48px; height:53px; background:#b9cbe3; border-radius:24px 24px 0 0; }
        .boxes { position:absolute; right:70px; bottom:0; width:85px; height:54px; background:linear-gradient(90deg,#F7D60A 0 32%,transparent 32% 36%,#e8c933 36% 68%,transparent 68% 72%,#d4b20a 72%); border:2px solid #b79500; opacity:.9; }
        .shelf { position:absolute; left:27px; bottom:0; width:105px; height:105px; border-left:7px solid #1C396A; border-right:7px solid #1C396A; background:repeating-linear-gradient(to bottom,transparent 0 26px,#1C396A 26px 32px); }
        .shelf:before { content:''; position:absolute; left:12px; top:7px; width:20px; height:88px; background:repeating-linear-gradient(to bottom,#F7D60A 0 18px,#f9e77a 18px 22px); box-shadow:30px 0 #1651A4,58px 0 #e8c933; opacity:.85; }
        .login-card { width:min(430px,100%); margin-top:22px; padding:25px 17px 18px; background:rgba(255,255,255,.92); border:1px solid rgba(255,255,255,.82); border-top:3px solid #F7D60A; border-radius:18px; box-shadow:0 16px 36px rgba(15,35,70,.28); backdrop-filter:blur(10px); }
        .login-card h2 { margin:0; text-align:center; color:#1C396A; font-size:18px; }
        .login-card .intro { margin:6px 0 20px; text-align:center; color:#66758b; font-size:11px; }
        .login-error { background:#fde4e4; border:1px solid #efb2b2; color:#D32F2F; padding:10px 12px; border-radius:8px; margin-bottom:14px; font-size:12px; }
        .login-error ul { margin:0; padding-left:17px; }
        .login-field { margin-bottom:14px; }
        .login-field label { display:block; margin-bottom:6px; color:#1C396A; font-size:11px; font-weight:800; }
        .field-shell { display:flex; align-items:center; gap:9px; height:43px; padding:0 12px; border:1px solid #d5dee9; border-radius:9px; background:rgba(255,255,255,.76); transition:border-color .2s ease, box-shadow .2s ease, background .2s ease; }
        .field-shell:focus-within { border-color:#0096FF; background:#fff; box-shadow:0 0 0 3px rgba(0,150,255,.13); }
        .field-icon { color:#8da1ba; font-size:17px; }
        .field-shell select, .field-shell input { width:100%; height:100%; border:0; outline:0; background:transparent; color:#212529; font-size:12px; }
        .password-toggle { border:0; background:transparent; color:#8494a8; cursor:pointer; padding:2px; font-size:16px; }
        .forgot { display:block; margin:-3px 0 14px; text-align:right; color:#1651A4; font-size:11px; font-weight:800; }
        .login-submit, .pimpinan-button { width:100%; height:42px; border-radius:8px; font-size:12px; font-weight:800; cursor:pointer; transition:transform .2s ease, box-shadow .2s ease, background .2s ease; }
        .login-submit { border:0; background:#1651A4; color:#fff; box-shadow:0 6px 12px rgba(22,81,164,.2); }
        .login-submit:hover { background:#1C396A; transform:translateY(-2px); box-shadow:0 12px 20px rgba(22,81,164,.28); }
        .divider { display:flex; align-items:center; gap:12px; margin:15px 0; color:#8190a3; font-size:11px; }
        .divider:before, .divider:after { content:''; flex:1; height:1px; background:#e2e8f0; }
        .pimpinan-button { border:1px solid #8eb5df; background:rgba(255,255,255,.55); color:#1651A4; }
        .pimpinan-button:hover { background:#fff8cf; border-color:#F7D60A; transform:translateY(-1px); box-shadow:0 6px 12px rgba(247,214,10,.18); }
        .login-footer { margin-top:14px; color:#8494a8; text-align:center; font-size:10px; }
        @media (max-height:760px) { .login-screen { padding-top:15px; } .warehouse-scene { height:105px; transform:scale(.82); margin:-6px auto -12px; } .login-card { padding-top:20px; } }
    </style>

    <main class="login-screen">
        <header class="login-brand">
            <div class="university-mark"><img src="{{ asset('images/Logo_Universitas_Terbuka.svg') }}" alt="Logo Universitas Terbuka"></div>
            <h1>MAGS-UT</h1>
            <p>Manajemen Gudang & Stok<br>Universitas Terbuka</p>
        </header>

        <div class="warehouse-scene" aria-hidden="true"><div class="shelf"></div><div class="warehouse-building"></div><div class="boxes"></div></div>

        <section class="login-card">
            <h2>Masuk ke Akun Anda</h2>
            <p class="intro">Silakan masuk menggunakan akun yang terdaftar</p>

            @if(session('status'))
                <div style="background:#e2f0e3;border:1px solid #9bc99e;color:#357A38;padding:10px 12px;border-radius:8px;margin-bottom:14px;font-size:12px;">{{ session('status') }}</div>
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
