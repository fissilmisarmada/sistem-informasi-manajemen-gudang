@extends('layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #1C396A;
            --primary-hover: #152b52;
            --accent-yellow: #F7D60A;
            --text-main: #0f172a;
            --text-muted: #d5dde9;
            --border-color: #cbd5e1;
            --bg-input: rgba(248, 250, 252, 0.8);
        }

        body {
            background: var(--primary-blue);
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow: hidden; /* Mencegah scroll di halaman */
        }

        .navbar { display: none !important; }
        .container { max-width: none !important; padding: 0 !important; }

        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background: url('/images/UT1.jpg') center/cover no-repeat fixed;
            position: relative;
        }

        .login-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(28, 57, 106, 0.75) 0%, rgba(15, 23, 42, 0.85) 100%);
            backdrop-filter: blur(6px);
        }

        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 380px;
            background: rgba(255, 255, 255, 0.50); /* Opasitas diturunkan agar tembus pandang */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-top: 4px solid var(--accent-yellow);
            border-radius: 20px;
            padding: 24px 28px; /* Padding dikecilkan */
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
        }

        .brand-header { text-align: center; margin-bottom: 20px; }
        .brand-header img { height: 50px; margin-bottom: 10px; drop-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .brand-header h1 { font-size: 20px; font-weight: 700; color: var(--primary-blue); margin: 0 0 4px; letter-spacing: -0.5px; }
        .brand-header p { font-size: 12px; color: var(--text-muted); margin: 0; line-height: 1.4; }

        .alert { padding: 10px 14px; border-radius: 10px; font-size: 12px; margin-bottom: 16px; font-weight: 500; }
        .alert-success { background: rgba(236, 253, 245, 0.9); border: 1px solid #a7f3d0; color: #065f46; }
        .alert-error { background: rgba(254, 242, 242, 0.9); border: 1px solid #fecaca; color: #991b1b; }
        .alert-error ul { margin: 0; padding-left: 20px; }

        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--primary-blue); margin-bottom: 6px; }

        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 12px; color: #64748b; width: 16px; height: 16px; pointer-events: none; }

        .input-control {
            width: 100%;
            padding: 10px 12px 10px 36px;
            font-size: 13px;
            color: var(--text-main);
            background: var(--bg-input);
            border: 1px solid rgba(203, 213, 225, 0.8);
            border-radius: 10px;
            transition: all 0.2s ease;
            appearance: none;
            font-family: inherit;
        }
        .input-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .select-wrapper::after {
            content: ''; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-size: cover;
        }

        .password-toggle {
            position: absolute; right: 10px; background: none; border: none; color: #64748b;
            cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;
        }
        .password-toggle:hover { color: var(--primary-blue); }
        .password-toggle svg { width: 16px; height: 16px; }

        .forgot-link {
            display: block; text-align: right; font-size: 11px; font-weight: 600;
            color: var(--primary-blue); text-decoration: none; margin: -4px 0 16px; transition: color 0.2s;
        }
        .forgot-link:hover { color: #3b82f6; text-decoration: underline; }

        .btn {
            width: 100%; padding: 10px 14px; font-size: 13px; font-weight: 600; border-radius: 10px;
            cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 6px; font-family: inherit;
        }
        .btn-primary {
            background: var(--primary-blue); color: white; border: none;
            box-shadow: 0 4px 10px rgba(28, 57, 106, 0.2);
        }
        .btn-primary:hover {
            background: var(--primary-hover); transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(28, 57, 106, 0.3);
        }

        .divider { display: flex; align-items: center; margin: 16px 0; color: #64748b; font-size: 11px; font-weight: 500; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid rgba(203, 213, 225, 0.6); }
        .divider::before { margin-right: 10px; } .divider::after { margin-left: 10px; }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.6); color: var(--primary-blue); border: 1px solid rgba(203, 213, 225, 0.8);
        }
        .btn-secondary:hover {
            background: rgba(248, 250, 252, 0.9); border-color: var(--primary-blue); transform: translateY(-1px);
        }
        .btn-secondary svg { width: 16px; height: 16px; }

        .footer-text { text-align: center; margin-top: 18px; font-size: 10px; color: var(--primary-blue); font-weight: 500; }
    </style>

    <main class="login-wrapper">
        <section class="login-card">
            <header class="brand-header">
                <img src="{{ asset('images/Logo_Universitas_Terbuka.svg') }}" alt="Logo Universitas Terbuka">
                <h1>MAGS-UT</h1>
                <p>Manajemen Gudang & Stok<br>Universitas Terbuka</p>
            </header>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.perform') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper select-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <select id="email" name="email" class="input-control" required>
                            <option value="" disabled selected hidden>Pilih email Anda</option>
                            @foreach($users as $user)
                                <option value="{{ $user->email }}" @selected(old('email') === $user->email)>{{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input type="password" id="password" name="password" class="input-control" placeholder="Masukkan password" required>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-slash-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <a class="forgot-link" href="{{ route('password.request') }}">Lupa password?</a>
                <button class="btn btn-primary" type="submit">Masuk</button>
            </form>

            <div class="divider">atau</div>

            <button class="btn btn-secondary" type="button" id="pimpinanLogin">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Masuk sebagai Pimpinan
            </button>

            <footer class="footer-text">&copy; 2026 Fissilmi & Ismail. All rights reserved.</footer>
        </section>
    </main>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.style.display = 'none';
                eyeSlashIcon.style.display = 'block';
            } else {
                password.type = 'password';
                eyeIcon.style.display = 'block';
                eyeSlashIcon.style.display = 'none';
            }
        });

        document.getElementById('pimpinanLogin').addEventListener('click', function () {
            const email = document.getElementById('email');
            const option = Array.from(email.options).find((item) => item.value.toLowerCase().includes('pimpinan'));

            if (option) {
                email.value = option.value;
                document.getElementById('password').focus();
            } else {
                alert('Akun pimpinan tidak ditemukan di daftar.');
            }
        });
    </script>
@endsection
