@extends('layouts.app')

@section('content')
<style>
    .page-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:20px; }
    .search-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px; }
    .search-bar input[type=text] { flex:1; min-width:200px; padding:12px 16px; border:2px solid #e2e8f0; border-radius:10px; font-size:15px; }
    .search-bar input:focus { outline:none; border-color:#3b82f6; }
    .search-bar select { padding:12px 14px; border:2px solid #e2e8f0; border-radius:10px; font-size:14px; }
    .search-bar select:focus { outline:none; border-color:#3b82f6; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:12px 20px; border-radius:10px; font-weight:700; font-size:14px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-primary:hover { background:#2563eb; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .btn-secondary:hover { background:#cbd5e1; }
    .result-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px; }
    .result-count { font-size:14px; color:#64748b; font-weight:600; }
    .section-title { font-size:16px; font-weight:800; color:#0f172a; margin:24px 0 12px; padding-bottom:8px; border-bottom:2px solid #f1f5f9; }
    .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:16px; }
    .result-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(15,23,42,.07); padding:16px; cursor:pointer; transition:transform .15s,box-shadow .15s; border:2px solid transparent; }
    .result-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(15,23,42,.12); border-color:#3b82f6; }
    .result-card .type-tag { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
    .type-buku { color:#7c3aed; }
    .type-barang { color:#0891b2; }
    .result-card .nama { font-size:15px; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .result-card .kode { font-size:11px; color:#94a3b8; margin-bottom:8px; }
    .result-card .meta { display:flex; flex-wrap:wrap; gap:5px; }
    .badge { display:inline-block; padding:3px 9px; border-radius:999px; font-size:11px; font-weight:700; }
    .badge-purple { background:#ede9fe; color:#6d28d9; }
    .badge-cyan { background:#cffafe; color:#0e7490; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .badge-gray { background:#f1f5f9; color:#475569; }
    .badge-yellow { background:#fef9c3; color:#854d0e; }
    .empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
    .empty-state .icon { font-size:48px; margin-bottom:12px; }
    .welcome-state { text-align:center; padding:60px 20px; }
    .welcome-state .icon { font-size:56px; margin-bottom:16px; }
    .welcome-state h2 { font-size:20px; font-weight:800; color:#0f172a; margin-bottom:8px; }
    .welcome-state p { color:#64748b; font-size:14px; }
    .tipe-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
    .tipe-tab { padding:8px 16px; border-radius:999px; font-size:13px; font-weight:700; cursor:pointer; border:2px solid #e2e8f0; background:#fff; color:#475569; text-decoration:none; }
    .tipe-tab.active { border-color:#3b82f6; background:#3b82f6; color:#fff; }
</style>

<div class="page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn-secondary" style="padding:8px 14px; font-size:13px;">← Kembali</a>
        <h1 class="page-title">Pencarian Barang Gudang</h1>
    </div>

<form method="GET" action="{{ route('pencarian.index') }}" class="search-bar">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama barang, kode, ISBN..." autofocus>
    <select name="kategori_id">
        <option value="">Semua Kategori</option>
        @foreach($kategoris as $kat)
            <option value="{{ $kat->id }}" {{ $kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary">🔍 Cari</button>
    @if($q)
        <a href="{{ route('pencarian.index') }}" class="btn btn-secondary">Reset</a>
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
            <p>Tidak ada hasil untuk "<strong>{{ $q }}</strong>"</p>
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
