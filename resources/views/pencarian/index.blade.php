@extends('layouts.app')

@section('content')
<style>
    .search-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; padding:14px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .search-bar input[type=text] { flex:1; min-width:200px; padding:12px 14px; border:1px solid #E8EAF0; border-radius:14px; font-size:14px; background:#FBFBFD; color:var(--andon-ink); transition:border-color .18s ease,box-shadow .18s ease,background .18s ease; }
    .search-bar input[type=text]:focus { outline:none; border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); background:#fff; }
    .search-bar select { padding:12px 14px; border:1px solid #E8EAF0; border-radius:14px; font-size:14px; background:#FBFBFD; color:var(--andon-ink); transition:border-color .18s ease,box-shadow .18s ease,background .18s ease; }
    .search-bar select:focus { outline:none; border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); background:#fff; }
    .result-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px; }
    .result-count { font-size:13px; color:var(--andon-muted); font-weight:600; }
    .section-title { font-size:14px; font-weight:800; color:var(--andon-ink); margin:22px 0 12px; padding-bottom:10px; border-bottom:1px solid var(--andon-line); letter-spacing:-.01em; }
    .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:14px; }
    .result-card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); padding:16px; cursor:pointer; transition:transform .20s cubic-bezier(.16,1,.3,1),box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease; }
    .result-card:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.05); border-color:#E8EAF0; }
    .result-card .type-tag { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; color:var(--andon-muted); }
    .type-buku { color:var(--andon-navy); }
    .type-barang { color:var(--andon-muted); }
    .result-card .nama { font-size:14px; font-weight:800; color:var(--andon-ink); margin-bottom:4px; letter-spacing:-.01em; }
    .result-card .kode { font-size:11px; color:var(--andon-faint); margin-bottom:10px; font-weight:600; }
    .result-card .meta { display:flex; flex-wrap:wrap; gap:6px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; border:1px solid transparent; }
    .badge-purple { background:var(--andon-navy); color:#fff; }
    .badge-cyan { background:#F8FAFC; color:var(--andon-navy); border-color:var(--andon-line); }
    .badge-green { background:var(--andon-green); color:#fff; }
    .badge-red { background:var(--andon-red); color:#fff; }
    .badge-gray { background:#F8FAFC; color:var(--andon-muted); border-color:var(--andon-line); }
    .badge-yellow { background:var(--andon-amber); color:var(--andon-amber-ink); border-color:#E6C200; }
    .empty-state { text-align:center; padding:48px 20px; color:var(--andon-faint); background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .empty-state .icon { font-size:40px; margin-bottom:10px; }
    .welcome-state { text-align:center; padding:48px 20px; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .welcome-state .icon { font-size:44px; margin-bottom:14px; }
    .welcome-state h2 { font-size:18px; font-weight:800; color:var(--andon-ink); margin:0 0 8px; letter-spacing:-.02em; }
    .welcome-state p { color:var(--andon-muted); font-size:13px; line-height:1.6; margin:0; }
    .tipe-tabs { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
    .tipe-tab { padding:8px 14px; border-radius:999px; font-size:13px; font-weight:700; cursor:pointer; border:1px solid var(--andon-line-strong); background:var(--andon-panel); color:var(--andon-muted); text-decoration:none; }
    .tipe-tab.active { border-color:var(--andon-ink); background:var(--andon-ink); color:#fff; }
    .page-header-row { display:flex; align-items:center; gap:16px; margin-bottom:18px; flex-wrap:wrap; }
</style>

<div class="page-header-row">
    <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn--ghost" style="padding:8px 14px; font-size:13px;">← Kembali</a>
    <h1 class="andon-page-title" style="margin:0">Pencarian Barang Gudang</h1>
</div>

<form method="GET" action="{{ route('pencarian.index') }}" class="search-bar">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama barang, kode, ISBN..." autofocus>
    <select name="kategori_id">
        <option value="">Semua Kategori</option>
        @foreach($kategoris as $kat)
            <option value="{{ $kat->id }}" {{ $kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn--primary">🔍 Cari</button>
    @if($q)
        <a href="{{ route('pencarian.index') }}" class="btn btn--ghost">Reset</a>
    @endif
</form>

@if($q)
    {{-- Filter tipe --}}
    <div class="tipe-tabs">
        <a href="{{ route('pencarian.index', array_merge(request()->query(), ['tipe' => 'semua'])) }}"
            class="tipe-tab {{ $tipe === 'semua' ? 'active' : '' }}">
            Semua ({{ $totalHasil }})
        </a>
        <a href="{{ route('pencarian.index', array_merge(request()->query(), ['tipe' => 'buku'])) }}"
            class="tipe-tab {{ $tipe === 'buku' ? 'active' : '' }}">
            📚 Buku ({{ $buku->count() }})
        </a>
        <a href="{{ route('pencarian.index', array_merge(request()->query(), ['tipe' => 'barang'])) }}"
            class="tipe-tab {{ $tipe === 'barang' ? 'active' : '' }}">
            📦 Barang ({{ $barangLain->count() }})
        </a>
    </div>

    @if($totalHasil === 0)
        <div class="empty-state">
            <div class="icon">🔍</div>
            <p>Tidak ada hasil untuk "<strong style="color:var(--andon-ink)">{{ $q }}</strong>"</p>
        </div>
    @else
        {{-- Hasil Buku --}}
        @if(($tipe === 'semua' || $tipe === 'buku') && $buku->isNotEmpty())
            <div class="section-title">📚 Buku ({{ $buku->count() }} hasil)</div>
            <div class="grid">
                @foreach($buku as $b)
                <div class="result-card" data-url="{{ route('barang.show', ['barang' => $b, 'from' => 'cari', 'q' => $q]) }}">
                    <div class="type-tag type-buku">Buku</div>
                    <div class="nama">{{ $b->nama }}</div>
                    <div class="kode">{{ $b->kode_barang }}</div>
                    <div class="meta">
                        <span class="badge badge-purple">{{ $b->kategori->nama }}</span>
                        @if($b->rak)
                            <span class="badge badge-gray">{{ $b->rak->kode_rak }}</span>
                        @else
                            <span class="badge badge-yellow">Tanpa rak</span>
                        @endif
                        <span class="badge badge-green">Stok: {{ $b->stok }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        {{-- Hasil Barang Lain --}}
        @if(($tipe === 'semua' || $tipe === 'barang') && $barangLain->isNotEmpty())
            <div class="section-title">📦 Barang ({{ $barangLain->count() }} hasil)</div>
            <div class="grid">
                @foreach($barangLain as $br)
                <div class="result-card" data-url="{{ route('barang.show', ['barang' => $br, 'from' => 'cari', 'q' => $q]) }}">
                    <div class="type-tag type-barang">{{ $br->kategori->nama }}</div>
                    <div class="nama">{{ $br->nama }}</div>
                    <div class="kode">{{ $br->kode_barang }}</div>
                    <div class="meta">
                        <span class="badge badge-cyan">{{ $br->kategori->nama }}</span>
                        @if($br->rak)
                            <span class="badge badge-gray">{{ $br->rak->kode_rak }}</span>
                        @else
                            <span class="badge badge-yellow">Tanpa rak</span>
                        @endif
                        @if($br->isStokMenipis())
                            <span class="badge badge-red">⚠ Menipis</span>
                        @else
                            <span class="badge badge-green">Stok: {{ $br->stok }} {{ $br->satuan }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    @endif
@else
    <div class="welcome-state">
        <div class="icon">🏭</div>
        <h2>Cari Barang di Seluruh Gudang</h2>
        <p>Masukkan nama barang, kode, atau ISBN untuk mencari di semua kategori:<br>
        Buku, ATK, Komputer, dan lainnya.</p>
    </div>
@endif
<script>
document.querySelectorAll('.result-card').forEach(card => {
    card.addEventListener('click', function() { window.location.href = this.dataset.url; });
    card.style.cursor = 'pointer';
});
</script>
@endsection
