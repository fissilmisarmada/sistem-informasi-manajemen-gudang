@extends('layouts.app')

@section('content')
<style>
    .detail-page{max-width:920px;margin:0 auto}
    .alert-success{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;padding:12px 14px;border-radius:12px;margin-bottom:14px;font-weight:600;font-size:13px}
    .hero-card{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);overflow:hidden;position:relative;display:grid;grid-template-columns:200px minmax(0,1fr);align-items:start;margin-bottom:14px;transition:box-shadow .22s}
    .hero-card:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .hero-card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy);z-index:1}
    .hero-media{background:#F8FAFC;border-right:1px solid #EDEEF2;display:grid;place-items:center;min-height:160px;aspect-ratio:1/1;overflow:hidden;position:relative;color:var(--andon-faint);font-size:48px}
    .hero-media img{width:100%;height:100%;object-fit:cover;display:block}
    .hero-body{padding:18px 20px 16px;display:flex;flex-direction:column;gap:0;min-width:0}
    .hero-actions{grid-column:1 / -1;display:flex;justify-content:flex-start;align-items:center;gap:8px;flex-wrap:wrap;padding:12px 16px;border-top:1px solid #F1F5F9;background:#FCFCFD}
    .hero-actions .btn{min-height:44px;padding:0 14px;gap:6px;font-size:13px;border-radius:12px}
    .hero-actions .btn svg{width:16px;height:16px;flex:0 0 auto}
    .hero-actions .btn--danger{margin-left:auto;position:relative}
    .hero-actions .btn--danger::before{content:'';position:absolute;left:-9px;top:18%;bottom:18%;width:1px;background:#E2E8F0}
    .meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px 16px;margin-top:14px;padding-top:14px;border-top:1px solid #F1F5F9}
    .meta-k{font-size:10px;font-weight:800;letter-spacing:.07em;color:var(--andon-faint);text-transform:uppercase}
    .meta-v{font-size:13px;font-weight:700;color:var(--andon-ink);margin-top:2px;word-break:break-word}
    .card{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);padding:18px 20px;margin-bottom:14px;overflow:hidden;position:relative;transition:box-shadow .22s}
    .card:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .card-title{font-size:12px;font-weight:800;letter-spacing:.08em;color:var(--andon-ink);margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--andon-line);text-transform:uppercase}
    .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;border:1px solid transparent}
    .badge-blue{background:#EFF6FF;color:#1E3A5F;border-color:#DBEAFE}
    .badge-green{background:#ECFDF5;color:#065F46;border-color:#A7F3D0}
    .badge-red{background:#FEF2F2;color:#991B1B;border-color:#FECACA}
    .badge-yellow{background:#FEFCE8;color:#854D0E;border-color:#FDE68A}
    .badge-gray{background:#F8FAFC;color:var(--andon-muted);border-color:var(--andon-line)}
    .btn--icon{width:auto}
    .btn--icon.btn--danger{background:#FFF1EE;border-color:#FECACA;color:var(--andon-red)}
    .btn--icon.btn--danger:hover{background:#FEE2E2;border-color:#FCA5A5}
    table{width:100%;border-collapse:collapse}
    th{background:#F8FAFC;padding:10px 12px;text-align:left;font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--andon-muted);border-bottom:1px solid var(--andon-line)}
    td{padding:10px 12px;border-bottom:1px solid #F1F5F9;font-size:13px;color:var(--andon-ink)}
    tr:last-child td{border-bottom:none}
    .stok-big{font-size:30px;font-weight:800;letter-spacing:-.04em;color:var(--andon-ink);line-height:1}
    .stok-unit{font-size:13px;color:var(--andon-muted);margin-left:4px;font-weight:700}
    @media(max-width:820px){ .hero-card{grid-template-columns:1fr} .hero-media{border-right:none;border-bottom:1px solid #EDEEF2;aspect-ratio:16/9;min-height:180px} .hero-body{padding:16px} .meta-grid{grid-template-columns:1fr} .hero-actions .btn{flex:1 1 auto;justify-content:center} .hero-actions .btn--danger{margin-left:0} .hero-actions .btn--danger::before{display:none} }
    @media(max-width:480px){ .hero-body{padding:14px} .stok-big{font-size:26px} .card{padding:14px 14px} .hero-actions{flex-wrap:nowrap;overflow-x:auto;overflow-y:hidden;justify-content:flex-start;gap:8px;padding:10px 12px;scrollbar-width:none;-webkit-overflow-scrolling:touch;scroll-snap-type:x mandatory} .hero-actions::-webkit-scrollbar{display:none} .hero-actions .btn{flex:0 0 auto;white-space:nowrap;padding:0 12px;min-height:40px;font-size:12.5px;scroll-snap-align:start} }
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
<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
    <a href="{{ $backUrl }}" class="back-link" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
    <span style="font-size:10px;font-weight:800;letter-spacing:.12em;color:var(--andon-faint);text-transform:uppercase">Detail Barang</span>
</div>

<div class="hero-card">
    <div class="hero-media" aria-hidden="true">
        @if($barang->gambar)
            <img src="{{ Storage::url($barang->gambar) }}" alt="{{ $barang->nama }}">
        @else
            <span style="font-size:56px">📦</span>
        @endif
    </div>
    <div class="hero-body">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div style="min-width:0;flex:1">
                <div style="font-size:10px;color:var(--andon-faint);font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px">{{ $barang->kode_barang }}</div>
                <h1 style="font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);margin:0 0 10px;line-height:1.15;word-break:break-word">{{ $barang->nama }}</h1>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                    <span class="badge badge-blue">{{ $barang->kategori->nama }}</span>
                    @if($barang->rak)<span class="badge badge-gray">Rak: {{ $barang->rak->kode_rak }}</span>@else<span class="badge badge-yellow">Belum ada rak</span>@endif
                    @if($barang->isStokMenipis())<span class="badge badge-red">Stok Menipis</span>@endif
                </div>
            </div>
            <div style="text-align:right;flex:0 0 auto">
                <div class="stok-big">{{ $barang->stok }}<span class="stok-unit">{{ $barang->satuan }}</span></div>
                @if($barang->stok_minimum > 0)<div style="font-size:11px;color:var(--andon-faint);font-weight:700;margin-top:4px">min. {{ $barang->stok_minimum }}</div>@endif
            </div>
        </div>
        <div class="meta-grid">
            <div><div class="meta-k">Gudang</div><div class="meta-v">{{ $barang->denahArea?->nama ?? $barang->rak?->denahArea?->nama ?? 'Belum ditempatkan' }}</div></div>
            <div><div class="meta-k">Lokasi rak</div><div class="meta-v">{{ $barang->rak ? $barang->rak->kode_rak . ' · ' . $barang->rak->nama_lokasi : 'Tanpa rak' }}</div></div>
            <div><div class="meta-k">Kategori</div><div class="meta-v">{{ $barang->kategori->nama }}</div></div>
            <div><div class="meta-k">Stok minimum</div><div class="meta-v">{{ $barang->stok_minimum > 0 ? $barang->stok_minimum . ' ' . $barang->satuan : '—' }}</div></div>
        </div>
        @if($barang->keterangan)
            @php $ket = $barang->keterangan; if (is_string($ket)) { $d = json_decode($ket, true); if (is_array($d)) $ket = $d; } @endphp
            @if(is_array($ket))
                <dl style="margin:12px 0 0;display:grid;grid-template-columns:auto 1fr;gap:4px 10px;font-size:13px;line-height:1.5">
                    @foreach($ket as $k=>$v)<dt style="color:var(--andon-faint);font-weight:700;text-transform:capitalize">{{ str_replace('_',' ', $k) }}</dt><dd style="margin:0;color:var(--andon-muted);word-break:break-word">{{ is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v }}</dd>@endforeach
                </dl>
            @else
                <p style="margin:12px 0 0;font-size:13px;color:var(--andon-muted);line-height:1.5;word-break:break-word">{{ $ket }}</p>
            @endif
        @endif
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
    <div class="hero-actions">
        <a href="{{ route('mutasi-barang.create', ['barang_id' => $barang->id]) }}" class="btn btn--primary" aria-label="Mutasi Stok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M16 3h5v5"/><path d="M21 3l-7 7"/><path d="M8 21h5v-5"/><path d="M3 21l7-7"/></svg> Mutasi</a>
        <a href="{{ route('stock-opname-barang.create', ['barang' => $barang, 'from' => 'detail-barang']) }}" class="btn btn--ghost" aria-label="Opname"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 2v5h5"/><path d="M9 12l2 2 4-4"/></svg> Opname</a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('barang.edit', $barang) }}" class="btn btn--amber" aria-label="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/></svg> Edit</a>
        <form method="POST" action="{{ route('barang.destroy', $barang) }}" onsubmit="return confirm('Hapus barang ini?')" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn--danger" aria-label="Hapus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4a1 1 0 011-1h6a1 1 0 011 1v2"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg> Hapus</button>
        </form>
        @endif
    </div>
    @endif
</div>

<div class="card">
    <div class="card-title">Riwayat Mutasi</div>
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
    <div class="card-title">Riwayat Opname</div>
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
