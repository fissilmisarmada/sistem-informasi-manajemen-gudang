@extends('layouts.app')

@section('content')
<style>
    .back-link { display:inline-flex; align-items:center; gap:8px; margin-bottom:20px; padding:9px 14px; background:#e2e8f0; color:#0f172a; border-radius:9px; font-weight:700; font-size:14px; text-decoration:none; }
    .back-link:hover { background:#cbd5e1; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .page-sub { font-size:14px; color:#64748b; margin-bottom:24px; }
    .grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
    .card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.08); padding:24px; }
    .card-title { font-size:15px; font-weight:800; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
    .info-row { display:flex; gap:12px; margin-bottom:10px; font-size:14px; }
    .info-label { color:#64748b; font-weight:700; min-width:120px; }
    .stok-big { font-size:36px; font-weight:900; color:#0f172a; text-align:center; padding:20px; }
    .stok-unit { font-size:16px; color:#64748b; }
    .form-group { margin-bottom:18px; }
    label { display:block; font-size:13px; font-weight:700; color:#475569; margin-bottom:6px; }
    input[type=number], textarea { width:100%; padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; }
    input:focus, textarea:focus { outline:none; border-color:#3b82f6; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .form-actions { display:flex; gap:10px; margin-top:20px; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:10px 12px; text-align:left; font-size:12px; font-weight:700; color:#475569; }
    td { padding:10px 12px; border-bottom:1px solid #f1f5f9; font-size:13px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    @media(max-width:700px) { .grid { grid-template-columns:1fr; } }
</style>

<a href="{{ request('from') === 'detail-barang' ? route('barang.show', $barang) : (request('from') === 'dashboard' ? (auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff')) : route('stock-opname-barang.index')) }}" class="back-link">← Kembali</a>

<h1 class="page-title">Opname: {{ $barang->nama }}</h1>
<p class="page-sub">{{ $barang->kode_barang }} · {{ $barang->kategori->nama }}</p>

<div class="grid">
    <div>
        <div class="card">
            <div class="card-title">Informasi Barang</div>
            <div class="stok-big">{{ $barang->stok }} <span class="stok-unit">{{ $barang->satuan }}</span></div>
            <p style="text-align:center;font-size:13px;color:#64748b;margin:0;">Stok tercatat di sistem</p>

            <div style="margin-top:20px;">
                <div class="info-row"><span class="info-label">Rak</span><span>{{ $barang->rak?->kode_rak ?? 'Belum ada rak' }}</span></div>
                <div class="info-row"><span class="info-label">Stok Minimum</span><span>{{ $barang->stok_minimum }} {{ $barang->satuan }}</span></div>
            </div>
        </div>

        @if($riwayat->isNotEmpty())
        <div class="card" style="margin-top:16px;">
            <div class="card-title">5 Opname Terakhir</div>
            <table>
                <thead><tr><th>Tanggal</th><th>Fisik</th><th>Selisih</th></tr></thead>
                <tbody>
                    @foreach($riwayat as $r)
                    <tr>
                        <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $r->jumlah_fisik }}</td>
                        <td>
                            @if($r->selisih == 0)
                                <span class="badge badge-green">0</span>
                            @else
                                <span class="badge badge-red">{{ $r->selisih }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="card">
        <div class="card-title">Input Hasil Opname</div>
        <form method="POST" action="{{ route('stock-opname-barang.store', $barang) }}">
            @csrf
            <div class="form-group">
                <label>Jumlah Fisik (hasil hitung) <span style="color:#ef4444">*</span></label>
                <input type="number" name="jumlah_fisik" value="{{ old('jumlah_fisik') }}" min="0" required
                    placeholder="Masukkan jumlah barang yang dihitung secara fisik">
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Catatan opname...">{{ old('keterangan') }}</textarea>
            </div>
            <div class="form-actions">
                <a href="{{ request('from') === 'detail-barang' ? route('barang.show', $barang) : (request('from') === 'dashboard' ? (auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff')) : route('stock-opname-barang.index')) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Opname</button>
            </div>
        </form>
    </div>
</div>
@endsection
