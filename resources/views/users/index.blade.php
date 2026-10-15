@extends('layouts.app')

@section('content')
<style>
    .users-page { max-width:1120px; margin:0 auto; }
    .andon-kicker { display:inline-flex; align-items:center; gap:8px; font-size:10px; font-weight:800; letter-spacing:.14em; color:var(--andon-faint); }
    .andon-kicker i { width:18px; height:2px; background:var(--andon-amber); display:inline-block; border-radius:999px; }
    .users-head { display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px; }
    .users-head h1 { margin:4px 0 0; font-size:28px; font-weight:800; letter-spacing:-.04em; color:var(--andon-ink); line-height:1; }
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
    .btn-delete { border:1px solid #FECACA; background:#FFF1EE; color:var(--andon-red); font-weight:800; font-size:13px; cursor:pointer; padding:0 14px; min-height:36px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; }
    .btn-delete:hover { background:#FEE2E2; }
    .btn-pass { border:1px solid #E8EAF0; background:#fff; color:var(--andon-ink); font-weight:800; font-size:13px; cursor:pointer; padding:0 14px; min-height:36px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; }
    .btn-pass:hover { background:#F8FAFC; }
    .aksi-group { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .pw-modal { display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(15,23,42,.45); }
    .pw-modal.active { display:flex; }
    .pw-modal__card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:16px; padding:18px; width:100%; max-width:420px; box-shadow:0 16px 40px rgba(15,23,42,.18); }
    .pw-modal__card h3 { margin:0 0 12px; font-size:14px; font-weight:800; color:var(--andon-ink); }
    .pw-actions { display:flex; gap:10px; margin-top:14px; }
    .muted { color:var(--andon-faint); font-size:12px; font-weight:600; }
    @media (max-width:900px) { .user-create-form { grid-template-columns:1fr 1fr; } .form-hint { grid-column:span 1; } }
    @media (max-width:560px) { .users-head h1 { font-size:24px; } .create-panel { padding:16px; border-radius:14px; } .user-create-form { grid-template-columns:1fr; } .form-hint { grid-column:auto; } .create-panel button { width:100%; } }
</style>

<div class="users-page">
    <div class="users-head">
        <a class="back-link" href="{{ route('dashboard.admin') }}" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
        <div>
            <span class="andon-kicker"><i></i> ADMINISTRASI</span>
            <h1>Manajemen User</h1>
        </div>
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
        <form method="POST" action="{{ route('users.store') }}" class="user-create-form" id="form-create-user">
            @csrf
            <div class="andon-field"><label for="name">NAMA</label><input class="andon-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap"></div>
            <div class="andon-field"><label for="new-email">EMAIL</label><input class="andon-control" type="email" id="new-email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.id"></div>
            <div class="andon-field"><label for="role">ROLE</label><select class="andon-control" id="role" name="role" required><option value="">Pilih role</option><option value="admin" @selected(old('role') === 'admin')>Admin</option><option value="staff" @selected(old('role') === 'staff')>Staff</option><option value="pimpinan" @selected(old('role') === 'pimpinan')>Pimpinan</option></select></div>
            <div class="andon-field"><label for="new-password">PASSWORD</label><input class="andon-control" type="password" id="new-password" name="password" required minlength="8" placeholder="••••••••"></div>
            <div class="form-hint">Pastikan email unik dan role sesuai hak akses.</div>
            <button type="button" class="btn btn--primary" onclick="document.getElementById('create-confirm-modal')?.classList.add('active');setTimeout(()=>document.getElementById('create-confirm')?.focus(),50)">Tambah User</button>
            <div id="create-confirm-modal" class="pw-modal" role="dialog" aria-modal="true" aria-label="Konfirmasi password">
                <div class="pw-modal__card">
                    <h3>Konfirmasi Password</h3>
                    <div class="andon-field"><label for="create-confirm">KONFIRMASI PASSWORD</label><input class="andon-control" type="password" id="create-confirm" name="password_confirmation" required minlength="8" placeholder="Ulangi password"></div>
                    <div class="pw-actions">
                        <button type="button" class="btn btn--ghost" onclick="document.getElementById('create-confirm-modal').classList.remove('active')">Batal</button>
                        <button type="submit" class="btn btn--primary">Konfirmasi &amp; Tambah</button>
                    </div>
                </div>
            </div>
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
                    <td data-label="Nama" style="font-weight:700;">{{ $user->name }}</td>
                    <td data-label="Email" style="color:var(--andon-muted);">{{ $user->email }}</td>
                    <td data-label="Role"><span class="role-badge">{{ ucfirst($user->role) }}</span></td>
                    <td data-label="Aksi">
                        <div class="aksi-group">
                            <button type="button" class="btn-pass" onclick="document.getElementById('edit-modal-{{ $user->id }}')?.classList.add('active')">Edit</button>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Hapus</button>
                                </form>
                            @else
                                <span class="muted">Akun aktif</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted" style="text-align:center; padding:24px;">Belum ada user.</td>
                </tr>
            @endforelse
        </tbody>
    </table></div>

    @foreach($users as $user)
        <div id="edit-modal-{{ $user->id }}" class="pw-modal" role="dialog" aria-modal="true" aria-label="Edit user {{ $user->name }}">
            <div class="pw-modal__card" style="max-width:480px">
                <h3>Edit User — {{ $user->name }}</h3>
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="andon-field" style="margin-bottom:10px"><label>NAMA</label><input class="andon-control" type="text" name="name" value="{{ $user->name }}" required maxlength="255"></div>
                    <div class="andon-field" style="margin-bottom:10px"><label>EMAIL</label><input class="andon-control" type="email" name="email" value="{{ $user->email }}" required></div>
                    <div class="andon-field" style="margin-bottom:10px"><label>ROLE</label><select class="andon-control" name="role" required><option value="admin" @selected($user->role==='admin')>Admin</option><option value="staff" @selected($user->role==='staff')>Staff</option><option value="pimpinan" @selected($user->role==='pimpinan')>Pimpinan</option></select></div>
                    <div class="andon-field" style="margin-bottom:10px"><label>PASSWORD BARU <span style="font-weight:600;color:var(--andon-faint)">(kosongkan jika tidak ganti)</span></label><input class="andon-control" type="password" name="password" minlength="8" placeholder="Minimal 8 karakter"></div>
                    <div class="andon-field"><label>KONFIRMASI PASSWORD</label><input class="andon-control" type="password" name="password_confirmation" minlength="8" placeholder="Ulangi password"></div>
                    <div class="pw-actions">
                        <button type="button" class="btn btn--ghost" onclick="document.getElementById('edit-modal-{{ $user->id }}').classList.remove('active')">Batal</button>
                        <button type="submit" class="btn btn--primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>
<script>
document.addEventListener('click', function(e){
    if(e.target.classList.contains('pw-modal')) e.target.classList.remove('active');
});
document.addEventListener('keydown', function(e){
    if(e.key==='Escape') document.querySelectorAll('.pw-modal.active').forEach(function(m){m.classList.remove('active')});
});
</script>
@endsection
