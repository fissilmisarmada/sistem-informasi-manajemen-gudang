@extends('layouts.app')

@section('content')
<style>
.detail-page{max-width:1120px;margin:0 auto}
.head{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.head h1{margin:4px 0 0;color:var(--andon-ink);font-size:23px;font-weight:800;letter-spacing:-.03em;line-height:1}
.meta{color:var(--andon-muted);font-size:12px;margin-top:6px}
.kicker{color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
.card{overflow:hidden;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);position:relative}
.card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
.card-head{padding:14px 16px 12px;border-bottom:1px solid var(--andon-line);display:flex;align-items:center;justify-content:space-between;gap:12px}
.card-head h2{margin:0;color:var(--andon-ink);font-size:13px;font-weight:800;letter-spacing:-.02em}
.card-head span{color:var(--andon-faint);font-size:11px;font-weight:700}
.list{padding:0}
.row{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #F1F5F9;font-size:13px}
.row:last-child{border-bottom:0}
.row a{color:var(--andon-navy);text-decoration:none;font-weight:700}
.row a:hover{color:var(--andon-ink);text-decoration:underline}
.badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#F8FAFC;color:var(--andon-muted);border:1px solid var(--andon-line)}
.empty{padding:32px 16px;text-align:center;color:var(--andon-muted);font-size:13px;font-weight:600}
</style>
<div class="detail-page">
    <a href="{{ route('denah-gudang') }}" class="btn btn--ghost" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">&larr; Kembali ke denah</a>
    <div class="head">
        <div><div class="kicker">Area gudang</div><h1>{{ $area->kode_area }} — {{ $area->nama }}</h1><div class="meta">{{ $area->keterangan ?? 'Area gudang' }} · {{ $area->barang->count() }} barang</div></div>
    </div>
    <div class="card">
        <div class="card-head"><h2>Barang di Area</h2><span>{{ $area->barang->count() }} item</span></div>
        <div class="list">
            @forelse($area->barang as $b)
            <div class="row">
                <div><a href="{{ route('barang.show', $b) }}">{{ $b->kode_barang }}</a> — {{ $b->nama }} <span class="badge">{{ $b->kategori?->nama ?? '-' }}</span></div>
                <strong style="white-space:nowrap">{{ $b->stok }} {{ $b->satuan }}</strong>
            </div>
            @empty
            <div class="empty">Belum ada barang di area ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
