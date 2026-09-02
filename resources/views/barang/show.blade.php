@extends('layouts.app')

@section('content')
<style>
    .back-link { display:inline-flex; align-items:center; gap:8px; margin-bottom:20px; padding:9px 14px; background:#e2e8f0; color:#0f172a; border-radius:9px; font-weight:700; font-size:14px; text-decoration:none; }
    .back-link:hover { background:#cbd5e1; }
    .detail-grid { display:grid; grid-template-columns:300px 1fr; gap:24px; align-items:start; }
    .detail-img { width:100%; border-radius:14px; object-fit:cover; background:#f1f5f9; min-height:200px; display:flex; align-items:center; justify-content:center; font-size:60px; }
    .detail-img img { width:100%; border-radius:14px; object-fit:cover; }
    .card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.08); padding:24px; margin-bottom:20px; }
    .card-title { font-size:16px; font-weight:800; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
    .info-row { display:flex; gap:12px; margin-bottom:10px; font-size:14px; }
    .info-label { color:#64748b; font-weight:700; min-width:140px; }
    .info-value { color:#0f172a; }
    .badge { display:inline-block; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-blue { background:#dbeafe; color:#1d4ed8; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .badge-yellow { background:#fef9c3; color:#854d0e; }
    .badge-gray { background:#f1f5f9; color:#475569; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:9px; font-weight:700; font-size:13px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-warning { background:#f59e0b; color:#fff; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-green { background:#22c55e; color:#fff; }
    .action-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:10px 12px; text-align:left; font-size:12px; font-weight:700; color:#475569; }
    td { padding:10px 12px; border-bottom:1px solid #f1f5f9; font-size:13px; }
    tr:last-child td { border-bottom:none; }
    .stok-big { font-size:32px; font-weight:900; color:#0f172a; }
    .stok-unit { font-size:14px; color:#64748b; margin-left:4px; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:12px 16px; border-radius:9px; margin-bottom:18px; font-weight:600; }
    @media(max-width:700px) { .detail-grid { grid-template-columns:1fr; } }
</style>

<a href="{{ route('barang.index') }}" class="back-link">← Kembali ke Daftar Barang</a>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="detail-grid">
    <div>
        <div class="detail-img">
            @if($barang->gambar)
                <img src="{{ Storage::url($barang->gambar) }}" alt="{{ $barang->nama }}">
            @else
                📦
            @endif
        </div>
    </div>

    <div>
        <div class="card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:12px;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:4px;">{{ $barang->kode_barang }}</div>
                    <h1 style="font-size:24px;font-weight:900;color:#0f172a;margin:0 0 10px;">{{ $barang->nama }}</h1>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <span class="badge badge-blue">{{ $barang->kategori->nama }}</span>
                        @if($barang->rak)
                            <span class="badge badge-gray">Rak: {{ $barang->rak->kode_rak }}</span>
                        @else
                            <span class="badge badge-yellow">Belum ada rak</span>
                        @endif
                        @if($barang->isStokMenipis())
                            <span class="badge badge-red">⚠ Stok Menipis</span>
                        @endif
                    </div>
                </div>
                <div style="text-align:right;">
                    <div class="stok-big">{{ $barang->stok }}<span class="stok-unit">{{ $barang->satuan }}</span></div>
                    @if($barang->stok_minimum > 0)
                        <div style="font-size:12px;color:#94a3b8;">min. {{ $barang->stok_minimum }}</div>
                    @endif
                </div>
            </div>

            @if($barang->keterangan)
                <p style="margin:14px 0 0;font-size:14px;color:#475569;">{{ $barang->keterangan }}</p>
            @endif
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <div class="action-bar">
            <a href="{{ route('mutasi-barang.create') }}?barang_id={{ $barang->id }}" class="btn btn-green">+ Mutasi Stok</a>
            <a href="{{ route('stock-opname-barang.create', $barang) }}" class="btn btn-primary">Opname</a>
            <a href="{{ route('barang.edit', $barang) }}" class="btn btn-warning">Edit</a>
            <form method="POST" action="{{ route('barang.destroy', $barang) }}" onsubmit="return confirm('Hapus barang ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Riwayat Mutasi --}}
<div class="card">
    <div class="card-title">Riwayat Mutasi Stok</div>
    @if($barang->mutasi->isEmpty())
        <p style="color:#94a3b8;font-size:14px;">Belum ada mutasi.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barang->mutasi->sortByDesc('created_at')->take(10) as $m)
                <tr>
                    <td>{{ $m->tanggal->format('d/m/Y') }}</td>
                    <td>
                        @if($m->jenis === 'masuk')
                            <span class="badge badge-green">Masuk</span>
                        @else
                            <span class="badge badge-red">Keluar</span>
                        @endif
                    </td>
                    <td>{{ $m->jumlah }} {{ $barang->satuan }}</td>
                    <td>{{ $m->keterangan ?? '-' }}</td>
                    <td>{{ $m->staff->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Riwayat Opname --}}
<div class="card">
    <div class="card-title">Riwayat Stock Opname</div>
    @if($barang->stockOpname->isEmpty())
        <p style="color:#94a3b8;font-size:14px;">Belum ada opname.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tercatat</th>
                    <th>Fisik</th>
                    <th>Selisih</th>
                    <th>Keterangan</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barang->stockOpname->sortByDesc('tanggal') as $op)
                <tr>
                    <td>{{ $op->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $op->jumlah_tercatat }}</td>
                    <td>{{ $op->jumlah_fisik }}</td>
                    <td>
                        @if($op->selisih == 0)
                            <span class="badge badge-green">0</span>
                        @else
                            <span class="badge badge-red">{{ $op->selisih > 0 ? '+' : '' }}{{ $op->selisih }}</span>
                        @endif
                    </td>
                    <td>{{ $op->keterangan ?? '-' }}</td>
                    <td>{{ $op->staff->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
