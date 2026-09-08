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

        .auth-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background: url('/images/UT1.jpg') center/cover no-repeat fixed;
            position: relative;
        }

        .auth-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(28, 57, 106, 0.75) 0%, rgba(15, 23, 42, 0.85) 100%);
            backdrop-filter: blur(6px);
        }

        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 380px;
            background: rgba(255, 255, 255, 0.50); /* Opasitas 50% agar tembus pandang */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-top: 4px solid var(--accent-yellow);
            border-radius: 20px;
            padding: 24px 28px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
        }

        .brand-header { text-align: center; margin-bottom: 20px; }
        .brand-header img { height: 45px; margin-bottom: 12px; drop-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .brand-header h1 { font-size: 20px; font-weight: 700; color: var(--primary-blue); margin: 0 0 6px; letter-spacing: -0.5px; }
        .brand-header p { font-size: 12px; color: var(--text-muted); margin: 0; line-height: 1.4; }

        .alert { padding: 10px 14px; border-radius: 10px; font-size: 12px; margin-bottom: 16px; font-weight: 500; }
        .alert-success { background: rgba(236, 253, 245, 0.9); border: 1px solid #a7f3d0; color: #065f46; }
        .alert-error { background: rgba(254, 242, 242, 0.9); border: 1px solid #fecaca; color: #991b1b; }

        .form-group { margin-bottom: 18px; }
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
            font-family: inherit;
        }
        .input-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

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

        .back-link {
            display: block; text-align: center; font-size: 12px; font-weight: 600;
            color: var(--text-muted); text-decoration: none; margin-top: 16px; transition: color 0.2s;
        }
        .back-link:hover { color: var(--primary-blue); }
    </style>

    <main class="auth-wrapper">
        <section class="auth-card">
            <!-- Header disamakan dengan login -->
            <header class="brand-header">
                <img src="{{ asset('images/Logo_Universitas_Terbuka.svg') }}" alt="Logo Universitas Terbuka">
                <h1>Lupa Password?</h1>
                <p>Masukkan email Anda. Kami akan mengirimkan tautan untuk membuat password baru.</p>
            </header>

            <!-- Menampilkan Alert Pesan -->
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first('email') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Akun</label>
                    <div class="input-wrapper">
                        <!-- Icon Amplop / Envelope -->
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <input id="email" name="email" type="email" class="input-control" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:16px; height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    Kirim Tautan Reset
                </button>
            </form>

            <a class="back-link" href="{{ route('login') }}">Batal</a>
        </section>
    </main>
@endsection
