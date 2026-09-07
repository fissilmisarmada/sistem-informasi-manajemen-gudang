@extends('layouts.app')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-primary:hover { background:#2563eb; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-danger:hover { background:#dc2626; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .btn-secondary:hover { background:#cbd5e1; }
    .filter-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; }
    .filter-bar input, .filter-bar select { padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; }
    .filter-bar input:focus, .filter-bar select:focus { outline:none; border-color:#3b82f6; }
    .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:18px; }
    .barang-card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.08); overflow:hidden; cursor:pointer; transition:transform .2s,box-shadow .2s; }
    .barang-card:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(15,23,42,.14); }
    .barang-img { width:100%; height:160px; object-fit:cover; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:40px; }
    .barang-img img { width:100%; height:160px; object-fit:cover; }
    .barang-body { padding:16px; }
    .barang-kode { font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; margin-bottom:4px; }
    .barang-nama { font-size:15px; font-weight:800; color:#0f172a; margin-bottom:8px; }
    .barang-meta { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
    .badge-blue { background:#dbeafe; color:#1d4ed8; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-yellow { background:#fef9c3; color:#854d0e; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .badge-gray { background:#f1f5f9; color:#475569; }
    .stok-info { font-size:13px; color:#475569; }
    .stok-info strong { color:#0f172a; }
    .alert { padding:12px 16px; border-radius:9px; margin-bottom:18px; font-weight:600; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
    .empty-state .icon { font-size:48px; margin-bottom:12px; }
    .pagination-wrap { margin-top:24px; }
    .pagination-wrap nav { display:flex; justify-content:center; }
    .pagination-wrap nav > div:first-child { display:none; }
    .pagination-wrap nav > div:last-child { display:flex; align-items:center; gap:5px; }
    .pagination-wrap nav a, .pagination-wrap nav span { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:34px; padding:0 10px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; color:#475569; font-size:13px; line-height:1; text-decoration:none; }
    .pagination-wrap nav a:hover { background:#eff6ff; border-color:#93c5fd; color:#1d4ed8; }
    .pagination-wrap nav span[aria-current="page"] { border-color:#2563eb; background:#2563eb; color:#fff; font-weight:800; }
    .pagination-wrap nav svg { display:block; width:16px; height:16px; }
    .pagination-wrap nav a[rel="prev"], .pagination-wrap nav a[rel="next"] { min-width:34px; padding:0; }
</style>

<div class="page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn-secondary" style="padding:8px 14px; font-size:13px;">← Kembali</a>
        <h1 class="page-title">Barang Gudang</h1>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <a href="{{ route('barang.create') }}" class="btn btn-primary">+ Tambah Barang</a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('barang.index') }}" class="filter-bar">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / kode barang...">
    <select name="kategori_id">
        <option value="">Semua Kategori</option>
        @foreach($kategoris as $kat)
            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
        @endforeach
    </select>
    <select name="rak_id">
        <option value="">Semua Rak</option>
        @foreach($raks as $rak)
            <option value="{{ $rak->id }}" {{ request('rak_id') == $rak->id ? 'selected' : '' }}>{{ $rak->kode_rak }} – {{ $rak->nama_lokasi }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary">Cari</button>
    @if(request()->hasAny(['q','kategori_id','rak_id']))
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

@if($barangs->isEmpty())
    <div class="empty-state">
        <div class="icon">📦</div>
        <p>Tidak ada barang ditemukan.</p>
    </div>
@else
    <div class="grid">
        @foreach($barangs as $barang)
        <div class="barang-card" data-url="{{ route('barang.show', ['barang' => $barang, 'from' => request('from')]) }}">
            <div class="barang-img">
                @if($barang->gambar)
                    <img src="{{ Storage::url($barang->gambar) }}" alt="{{ $barang->nama }}">
                @else
                    📦
                @endif
            </div>
            <div class="barang-body">
                <div class="barang-kode">{{ $barang->kode_barang }}</div>
                <div class="barang-nama">{{ $barang->nama }}</div>
                <div class="barang-meta">
                    <span class="badge badge-blue">{{ $barang->kategori->nama }}</span>
                    @if($barang->rak)
                        <span class="badge badge-gray">{{ $barang->rak->kode_rak }}</span>
                    @endif
                    @if($barang->isStokMenipis())
                        <span class="badge badge-red">Stok Menipis</span>
                    @endif
                </div>
                <div class="stok-info">
                    Stok: <strong>{{ $barang->stok }} {{ $barang->satuan }}</strong>
                    @if($barang->stok_minimum > 0)
                        <span style="color:#94a3b8"> / min {{ $barang->stok_minimum }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-wrap">{{ $barangs->links() }}</div>
@endif
<script>
document.querySelectorAll('.barang-card').forEach(card => {
    card.addEventListener('click', function() { window.location.href = this.dataset.url; });
});
</script>
@endsection
