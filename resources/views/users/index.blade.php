@extends('layouts.app')

@section('content')
    <div style="padding:24px 0;">
    <a class="back-dashboard" href="{{ route('dashboard.admin') }}">&larr; Kembali ke Dashboard</a>
    <div style="margin-bottom:22px;">
        <div style="font-size:12px;letter-spacing:1px;color:#64748b;text-transform:uppercase;font-weight:700;">Administrasi</div>
        <h1 style="margin:8px 0 0;font-size:32px;color:#0f172a;">Manajemen User</h1>
    </div>

    @if(session('sukses'))
        <div style="padding:12px 14px;background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:10px;margin-bottom:16px;font-weight:600;">{{ session('sukses') }}</div>
    @endif
    @if($errors->any())
        <div style="padding:12px 14px;background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;margin-bottom:16px;">
            <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;align-items:end;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;margin-bottom:24px;box-shadow:0 8px 24px rgba(15,23,42,.05);">
        @csrf
        <div><label for="name" style="display:block;margin-bottom:7px;font-weight:600;color:#374151;">Nama</label><input id="name" name="name" value="{{ old('name') }}" required style="width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:9px;box-sizing:border-box;"></div>
        <div><label for="new-email" style="display:block;margin-bottom:7px;font-weight:600;color:#374151;">Email</label><input type="email" id="new-email" name="email" value="{{ old('email') }}" required style="width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:9px;box-sizing:border-box;"></div>
        <div><label for="role" style="display:block;margin-bottom:7px;font-weight:600;color:#374151;">Role</label><select id="role" name="role" required style="width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:9px;background:#fff;box-sizing:border-box;"><option value="">Pilih role</option><option value="admin" @selected(old('role') === 'admin')>Admin</option><option value="staff" @selected(old('role') === 'staff')>Staff</option><option value="pimpinan" @selected(old('role') === 'pimpinan')>Pimpinan</option></select></div>
        <div><label for="new-password" style="display:block;margin-bottom:7px;font-weight:600;color:#374151;">Password</label><input type="password" id="new-password" name="password" required minlength="8" style="width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:9px;box-sizing:border-box;"></div>
        <div><label for="password-confirmation" style="display:block;margin-bottom:7px;font-weight:600;color:#374151;">Konfirmasi Password</label><input type="password" id="password-confirmation" name="password_confirmation" required minlength="8" style="width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:9px;box-sizing:border-box;"></div>
        <div style="grid-column:span 3;color:#64748b;font-size:13px;align-self:center;">Password minimal 8 karakter.</div>
        <button type="submit" style="padding:11px 16px;background:#0f172a;color:#fff;border:0;border-radius:9px;font-weight:700;cursor:pointer;">Tambah User</button>
    </form>

    <div style="overflow-x:auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;"><table cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;min-width:720px;">
        <thead>
            <tr>
                <th style="text-align:left;padding:14px;color:#475569;">Nama</th>
                <th style="text-align:left;padding:14px;color:#475569;">Email</th>
                <th style="text-align:left;padding:14px;color:#475569;">Role</th>
                <th style="text-align:left;padding:14px;color:#475569;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="padding:14px;font-weight:700;">{{ $user->name }}</td>
                    <td style="padding:14px;">{{ $user->email }}</td>
                    <td style="padding:14px;">{{ ucfirst($user->role) }}</td>
                    <td style="padding:14px;">
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="border:0;background:none;color:#dc2626;font-weight:700;cursor:pointer;padding:0;">Hapus</button>
                            </form>
                        @else
                            <span style="color:#94a3b8;font-size:13px;">Akun aktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:14px;">Belum ada user.</td>
                </tr>
            @endforelse
        </tbody>
    </table></div>
    </div>
@endsection
