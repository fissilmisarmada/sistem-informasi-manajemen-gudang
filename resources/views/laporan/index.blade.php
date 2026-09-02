@extends('layouts.app')

@section('content')
    <div style="padding:24px 0;">
        <a class="back-dashboard" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}">&larr; Kembali</a>
        <div style="display:flex;justify-content:space-between;align-items:end;gap:16px;margin-bottom:22px;">
            <div>
                <div style="font-size:12px;letter-spacing:1px;color:#64748b;text-transform:uppercase;font-weight:700;">Monitoring Sistem</div>
                <h1 style="margin:8px 0 0;font-size:32px;color:#0f172a;">Laporan</h1>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                <a href="{{ route('laporan.export') }}" style="padding:12px 18px;background:#0f172a;color:#fff;border-radius:10px;font-weight:700;">Export CSV</a>
            @endif
        </div>

        @if(session('sukses'))
            <div style="padding:12px 14px;background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:10px;margin-bottom:16px;font-weight:600;">{{ session('sukses') }}</div>
        @endif
        @if($errors->any())
            <div style="padding:12px 14px;background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;margin-bottom:16px;">{{ $errors->first() }}</div>
        @endif

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:16px;margin-bottom:24px;">
            @foreach([['Total Buku',$ringkasan['buku'],'#2563eb'],['Ditempatkan',$ringkasan['buku_ditempatkan'],'#059669'],['Total Rak',$ringkasan['rak'],'#d97706'],['Penempatan',$ringkasan['penempatan'],'#7c3aed'],['Stock Opname',$ringkasan['opname'],'#e11d48']] as $stat)
                <div style="background:{{ $stat[2] }};color:#fff;border-radius:14px;padding:22px;box-shadow:0 12px 30px rgba(0,0,0,.15);">
                    <div style="font-size:13px;opacity:.8;">{{ $stat[0] }}</div>
                    <div style="font-size:34px;font-weight:800;margin-top:12px;">{{ $stat[1] }}</div>
                </div>
            @endforeach
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
            <form method="POST" action="{{ route('laporan.import') }}" enctype="multipart/form-data" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px;margin-bottom:24px;">
                @csrf
                <div><label for="file" style="display:block;margin-bottom:7px;font-weight:700;color:#374151;">Import laporan CSV</label><input type="file" id="file" name="file" accept=".csv,.txt" required></div>
                <button type="submit" style="padding:11px 16px;background:#2563eb;color:#fff;border:0;border-radius:9px;font-weight:700;cursor:pointer;">Import CSV</button>
                <span style="font-size:13px;color:#64748b;">Gunakan file hasil export laporan.</span>
            </form>
        @endif

        <div style="overflow-x:auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;"><table cellpadding="8" cellspacing="0" style="width:100%;min-width:800px;border-collapse:collapse;">
            <thead><tr><th style="text-align:left;padding:14px;color:#475569;">Kode</th><th style="text-align:left;padding:14px;color:#475569;">Judul</th><th style="text-align:left;padding:14px;color:#475569;">ISBN</th><th style="text-align:left;padding:14px;color:#475569;">Rak</th><th style="text-align:left;padding:14px;color:#475569;">Halaman</th></tr></thead>
            <tbody>
                @forelse($bukus as $buku)
                    <tr><td style="padding:14px;font-weight:700;">{{ $buku->kode_buku }}</td><td style="padding:14px;">{{ $buku->judul }}</td><td style="padding:14px;">{{ $buku->isbn ?? '-' }}</td><td style="padding:14px;">{{ $buku->rak?->kode_rak ?? 'Belum ditempatkan' }}</td><td style="padding:14px;">{{ $buku->jumlah_halaman ?? '-' }}</td></tr>
                @empty
                    <tr><td colspan="5" style="padding:18px;color:#64748b;">Belum ada data buku.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
@endsection
