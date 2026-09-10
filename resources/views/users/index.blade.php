@extends('layouts.app')

@section('content')
<style>
    .users-page { max-width:1120px; margin:0 auto; }
    .andon-kicker { display:inline-flex; align-items:center; gap:8px; font-size:10px; font-weight:800; letter-spacing:.14em; color:var(--andon-faint); }
    .andon-kicker i { width:18px; height:2px; background:var(--andon-amber); display:inline-block; border-radius:999px; }
    .users-head { margin-bottom:18px; }
    .users-head h1 { margin:8px 0 0; font-size:28px; font-weight:800; letter-spacing:-.04em; color:var(--andon-ink); line-height:1; }
    .alert { padding:12px 14px; border-radius:12px; margin-bottom:16px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:10px; }
    .alert-success { background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; }
    .alert-error { background:#FEF2F2; color:#991B1B; border:1px solid #FECACA; }
    .alert-error ul { margin:6px 0 0; padding-left:18px; }
    .create-panel { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; padding:20px; margin-bottom:22px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); position:relative; overflow:hidden; }
    .create-panel::before { content:''; position:absolute; left:0; right:0; top:0; height:3px; background:var(--andon-amber); }
    .create-panel__head { margin-bottom:14px; }
    .create-panel__head h2 { margin:0; font-size:12px; font-weight:800; letter-spacing:.08em; color:var(--andon-ink); }
    .create-panel__head p { margin:4px 0 0; font-size:12px; color:var(--andon-muted); }
    .user-create-form { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; align-items:end; }
    .andon-field label { display:block; margin-bottom:8px; font-size:11px; font-weight:700; letter-spacing:.06em; color:var(--andon-muted); }
    .andon-control { width:100%; padding:12px 14px; font-size:13px; font-weight:600; color:var(--andon-ink); background:#FBFBFD; border:1px solid #E8EAF0; border-radius:14px; outline:none; transition:border-color .18s,box-shadow .18s,background .18s; font-family:inherit; box-sizing:border-box; appearance:none; }
    .andon-control:focus { background:#fff; border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); }
    .form-hint { grid-column:span 3; color:var(--andon-muted); font-size:12px; font-weight:600; align-self:center; }
    .table-wrap { overflow-x:auto; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    table { border-collapse:collapse; width:100%; min-width:720px; }
    th { text-align:left; padding:12px 14px; background:#F8FAFC; color:var(--andon-muted); font-size:11px; font-weight:800; letter-spacing:.06em; border-bottom:1px solid var(--andon-line); }
    td { padding:13px 14px; border-top:1px solid #F1F5F9; font-size:13px; color:var(--andon-ink); }
    tbody tr:hover td { background:#F8FAFC; }
    .role-badge { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; border:1px solid var(--andon-line); background:#F8FAFC; color:var(--andon-ink); letter-spacing:.04em; }
    .btn-delete { border:0; background:none; color:var(--andon-red); font-weight:800; font-size:12px; cursor:pointer; padding:0; }
    .btn-delete:hover { text-decoration:underline; }
    .muted { color:var(--andon-faint); font-size:12px; font-weight:600; }
    @media (max-width:900px) { .user-create-form { grid-template-columns:1fr 1fr; } .form-hint { grid-column:span 1; } }
    @media (max-width:560px) { .users-head h1 { font-size:24px; } .create-panel { padding:16px; border-radius:14px; } .user-create-form { grid-template-columns:1fr; } .form-hint { grid-column:auto; } .create-panel button { width:100%; } }
</style>

<div class="users-page">
    <div style="margin-bottom:14px;">
        <a class="btn btn--ghost" href="{{ route('dashboard.admin') }}" style="min-height:36px;padding:0 14px;font-size:12px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Kembali
        </a>
    </div>
    <div class="users-head">
        <span class="andon-kicker"><i></i> ADMINISTRASI</span>
        <h1>Manajemen User</h1>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success">{{ session('sukses') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="create-panel">
        <div class="create-panel__head">
            <h2>TAMBAH USER BARU</h2>
            <p>Buat akun admin, staff, atau pimpinan. Password minimal 8 karakter.</p>
        </div>
        <form method="POST" action="{{ route('users.store') }}" class="user-create-form">
            @csrf
            <div class="andon-field"><label for="name">NAMA</label><input class="andon-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap"></div>
            <div class="andon-field"><label for="new-email">EMAIL</label><input class="andon-control" type="email" id="new-email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.id"></div>
            <div class="andon-field"><label for="role">ROLE</label><select class="andon-control" id="role" name="role" required><option value="">Pilih role</option><option value="admin" @selected(old('role') === 'admin')>Admin</option><option value="staff" @selected(old('role') === 'staff')>Staff</option><option value="pimpinan" @selected(old('role') === 'pimpinan')>Pimpinan</option></select></div>
            <div class="andon-field"><label for="new-password">PASSWORD</label><input class="andon-control" type="password" id="new-password" name="password" required minlength="8" placeholder="••••••••"></div>
            <div class="andon-field"><label for="password-confirmation">KONFIRMASI PASSWORD</label><input class="andon-control" type="password" id="password-confirmation" name="password_confirmation" required minlength="8" placeholder="Ulangi password"></div>
            <div class="form-hint">Pastikan email unik dan role sesuai hak akses.</div>
            <button type="submit" class="btn btn--primary">Tambah User</button>
        </form>
    </div>

    <div class="table-wrap"><table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="font-weight:700;">{{ $user->name }}</td>
                    <td style="color:var(--andon-muted);">{{ $user->email }}</td>
                    <td><span class="role-badge">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        @else
                            <span class="muted">Akun aktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted" style="text-align:center; padding:24px;">Belum ada user.</td>
                </tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endsection
