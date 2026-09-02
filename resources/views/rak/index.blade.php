@extends('layouts.app')

@section('content')
    <div style="padding:24px 0;">
        <a class="back-dashboard" href="{{ auth()->user()->isAdmin() || auth()->user()->isStaff() ? route('denah-gudang') : route('dashboard.pimpinan') }}">&larr; Kembali</a>
        <div style="display:flex;justify-content:space-between;align-items:end;gap:16px;margin-bottom:22px;">
            <div>
                <div style="font-size:12px;letter-spacing:1px;color:#64748b;text-transform:uppercase;font-weight:700;">Master Data</div>
                <h1 style="margin:8px 0 0;font-size:32px;color:#0f172a;">Daftar Rak</h1>
            </div>
            <div style="color:#64748b;font-size:14px;">{{ $dataRak->count() }} lokasi terdaftar</div>
        </div>

        @if(session('sukses'))
            <div style="padding:12px 14px;background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:10px;margin-bottom:16px;font-weight:600;">{{ session('sukses') }}</div>
        @endif
        @if($errors->any())
            <div style="padding:12px 14px;background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;margin-bottom:16px;">{{ $errors->first() }}</div>
        @endif

    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
    <form method="POST" action="{{ route('rak.store') }}" style="margin-bottom:24px;display:grid;grid-template-columns:repeat(3,1fr) auto;gap:12px;align-items:end;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;box-shadow:0 8px 24px rgba(15,23,42,.05);">
        @csrf
        <div>
            <label for="kode_rak">Kode Rak</label>
            <input type="text" name="kode_rak" id="kode_rak" required style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
        <div>
            <label for="nama_lokasi">Nama Lokasi</label>
            <input type="text" name="nama_lokasi" id="nama_lokasi" required style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
        <div>
            <label for="kapasitas">Kapasitas</label>
            <input type="number" name="kapasitas" id="kapasitas" min="0" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
        <div>
            <button type="submit" style="padding:11px 16px;background:#0f172a;color:#fff;border:none;border-radius:9px;cursor:pointer;font-weight:700">Tambah Rak</button>
        </div>
    </form>
    @endif

    @if($dataRak->isEmpty())
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;color:#64748b;">Belum ada data rak.</div>
    @else
        <div style="overflow-x:auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;">
        <table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;min-width:760px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:14px;color:#475569;">Kode Rak</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Lokasi</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Kapasitas</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataRak as $rak)
                    <tr>
                        <td style="padding:14px;font-weight:700;color:#0f172a;">{{ $rak->kode_rak }}</td>
                        <td style="padding:14px;">{{ $rak->nama_lokasi }}</td>
                        <td style="padding:14px;">{{ $rak->kapasitas ?? '-' }}</td>
                        <td style="padding:14px;white-space:nowrap;">
                            <a href="{{ route('rak.show', $rak) }}" style="margin-right:10px;color:#2563eb;font-weight:700">Detail</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                                <a href="{{ route('stock-opname.create', $rak) }}" style="margin-right:10px;color:#0f766e;font-weight:700">Opname</a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('rak.destroy', $rak) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus rak ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border:0;background:none;color:#dc2626;font-weight:700;cursor:pointer;padding:0">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
    </div>
@endsection
