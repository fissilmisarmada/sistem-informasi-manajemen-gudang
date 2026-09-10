@extends('layouts.app')

@section('content')
<style>
    .detail-page{max-width:1120px;margin:0 auto}
    .alert-success{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;padding:12px 14px;border-radius:12px;margin-bottom:16px;font-weight:600;font-size:13px}
    .detail-grid{display:grid;grid-template-columns:280px minmax(0,1fr);gap:16px;align-items:start;margin-bottom:16px}
    .detail-img{width:100%;border-radius:20px;background:var(--andon-panel);border:1px solid #EDEEF2;overflow:hidden;min-height:200px;display:flex;align-items:center;justify-content:center;font-size:52px;color:var(--andon-faint);box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);transition:box-shadow .22s cubic-bezier(.16,1,.3,1)}
    .detail-img:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .detail-img img{width:100%;height:100%;object-fit:cover;display:block}
    .card{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);padding:20px;margin-bottom:16px;overflow:hidden;position:relative;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .card:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .card--amber::before{background:var(--andon-amber)}
    .card--red::before{background:var(--andon-red)}
    .card-title{font-size:12px;font-weight:800;letter-spacing:.08em;color:var(--andon-ink);margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--andon-line)}
    .info-row{display:flex;gap:12px;margin-bottom:8px;font-size:13px}
    .info-label{color:var(--andon-muted);font-weight:700;min-width:132px}
    .info-value{color:var(--andon-ink);font-weight:600}
    .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;border:1px solid transparent}
    .badge-blue{background:#EFF6FF;color:#1E3A5F;border-color:#DBEAFE}
    .badge-green{background:#ECFDF5;color:#065F46;border-color:#A7F3D0}
    .badge-red{background:#FEF2F2;color:#991B1B;border-color:#FECACA}
    .badge-yellow{background:#FEFCE8;color:#854D0E;border-color:#FDE68A}
    .badge-gray{background:#F8FAFC;color:var(--andon-muted);border-color:var(--andon-line)}
    .action-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
    table{width:100%;border-collapse:collapse}
    th{background:#F8FAFC;padding:10px 12px;text-align:left;font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--andon-muted);border-bottom:1px solid var(--andon-line)}
    td{padding:10px 12px;border-bottom:1px solid #F1F5F9;font-size:13px;color:var(--andon-ink)}
    tr:last-child td{border-bottom:none}
    .stok-big{font-size:30px;font-weight:800;letter-spacing:-.04em;color:var(--andon-ink);line-height:1}
    .stok-unit{font-size:13px;color:var(--andon-muted);margin-left:4px;font-weight:700}
    @media(max-width:700px){.detail-grid{grid-template-columns:1fr}}
</style>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@php
    $prev = url()->previous();
    $dashboardUrl = auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan'));
    if (request('from') === 'cari') {
        $backUrl = route('pencarian.index', ['q' => request('q')]);
        session(['valid_back_url' => $backUrl]);
    } elseif (request('from') === 'dashboard') {
        $backUrl = $dashboardUrl;
        session(['valid_back_url' => $backUrl]);
    } else {
        if (!str_contains($prev, 'mutasi-barang') && !str_contains($prev, 'edit') && !str_contains($prev, 'stock-opname') && $prev !== url()->current()) {
            session(['valid_back_url' => $prev]);
        }
        $backUrl = session('valid_back_url', route('barang.index'));
    }
@endphp

<div class="detail-page">
<a href="{{ $backUrl }}" class="btn btn--ghost" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">
    ← Kembali
</a>

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
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
                <div style="min-width:0">
                    <div style="font-size:10px;color:var(--andon-faint);font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px">{{ $barang->kode_barang }}</div>
                    <h1 style="font-size:23px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);margin:0 0 10px;line-height:1">{{ $barang->nama }}</h1>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <span class="badge badge-blue">{{ $barang->kategori->nama }}</span>
                        @if($barang->rak)
                            <span class="badge badge-gray">Rak: {{ $barang->rak->kode_rak }}</span>
                        @else
                            <span class="badge badge-yellow">Belum ada rak</span>
                        @endif
                        @if($barang->isStokMenipis())
                            <span class="badge badge-red">Stok Menipis</span>
                        @endif
                    </div>
                </div>
                <div style="text-align:right;flex:0 0 auto">
                    <div class="stok-big">{{ $barang->stok }}<span class="stok-unit">{{ $barang->satuan }}</span></div>
                    @if($barang->stok_minimum > 0)
                        <div style="font-size:11px;color:var(--andon-faint);font-weight:700;margin-top:4px">min. {{ $barang->stok_minimum }}</div>
                    @endif
                </div>
            </div>
            @if($barang->keterangan)
                <p style="margin:14px 0 0;font-size:13px;color:var(--andon-muted);line-height:1.5">{{ $barang->keterangan }}</p>
            @endif
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <div class="action-bar">
            <a href="{{ route('mutasi-barang.create', ['barang_id' => $barang->id]) }}" class="btn btn--primary">Mutasi Stok</a>
            <a href="{{ route('barang.edit', $barang) }}" class="btn btn--amber">Edit</a>
            <a href="{{ route('stock-opname-barang.create', ['barang' => $barang, 'from' => 'detail-barang']) }}" class="btn btn--ghost">Opname</a>
            <form method="POST" action="{{ route('barang.destroy', $barang) }}" onsubmit="return confirm('Hapus barang ini?')" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn--ghost" style="color:var(--andon-red);border-color:#FECACA">Hapus</button>
            </form>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-title">Riwayat Mutasi Stok</div>
    @if($barang->mutasi->isEmpty())
        <p style="color:var(--andon-faint);font-size:13px;font-weight:600;margin:0">Belum ada mutasi.</p>
    @else
        <div style="overflow-x:auto">
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
                @foreach($barang->mutasi->sortByDesc('tanggal')->take(10) as $m)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}</td>
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
        </div>
    @endif
</div>

<div class="card">
    <div class="card-title">Riwayat Stock Opname</div>
    @if($barang->stockOpname->isEmpty())
        <p style="color:var(--andon-faint);font-size:13px;font-weight:600;margin:0">Belum ada opname.</p>
    @else
        <div style="overflow-x:auto">
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
        </div>
    @endif
</div>

</div>

@endsection
