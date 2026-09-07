@extends('layouts.app')

@section('content')
    <div style="padding:24px 0;">
    <a class="back-dashboard" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}">&larr; Kembali ke Dashboard</a>
    <div style="margin-bottom:22px;">
        <div style="font-size:12px;letter-spacing:1px;color:#64748b;text-transform:uppercase;font-weight:700;">Kontrol Inventaris</div>
        <h1 style="margin:8px 0 0;font-size:32px;color:#0f172a;">Stock Opname</h1>
    </div>

    @if(session('sukses'))
        <div style="padding:12px 14px;background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:10px;margin-bottom:16px;font-weight:600;">{{ session('sukses') }}</div>
    @endif

    @if($raks->isEmpty())
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;color:#64748b;">Belum ada rak untuk dilakukan stock opname.</div>
    @else
        <div style="overflow-x:auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;"><table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;min-width:760px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:14px;color:#475569;">Kode Rak</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Lokasi</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Jumlah Buku</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Riwayat</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($raks as $rak)
                    <tr>
                        <td style="padding:14px;font-weight:700;">{{ $rak->kode_rak }}</td>
                        <td style="padding:14px;">{{ $rak->nama_lokasi }}</td>
                        <td style="padding:14px;">{{ $rak->buku->count() }}</td>
                        <td style="padding:14px;">{{ $rak->stockOpname->count() }}</td>
                        <td>
                            <a href="{{ route('stock-opname.create', $rak) }}" style="color:#2563eb;font-weight:700;margin-right:10px;">Lakukan Opname</a>
                            <a href="{{ route('stock-opname.riwayat', $rak) }}" style="color:#0f766e;font-weight:700;">Riwayat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    @endif
    </div>
@endsection
