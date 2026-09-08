@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .barang-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 24px 16px 48px;
        font-family: 'Inter', sans-serif;
    }

    /* Page Header */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .header-title-group {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .page-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin: 0;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-primary {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.28);
    }
    .btn-secondary {
        background: #fff;
        color: #475569;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .btn-secondary:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    /* Alert */
    .alert {
        padding: 14px 18px;
        border-radius: 14px;
        margin-bottom: 24px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    /* Filter Card */
    .filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        margin-bottom: 28px;
    }
    .filter-bar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }
    .filter-bar input[type="text"],
    .filter-bar select {
        padding: 10px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13px;
        color: #0f172a;
        background: #fff;
        outline: none;
        transition: all 0.2s ease;
    }
    .filter-bar input[type="text"] {
        flex: 2;
        min-width: 200px;
    }
    .filter-bar select {
        flex: 1;
        min-width: 150px;
    }
    .filter-bar input:focus,
    .filter-bar select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    /* Grid & Cards */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }
    .barang-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 18px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
    }
    .barang-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    /* Card Image */
    .barang-img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 42px;
        border-bottom: 1px solid #f1f5f9;
        position: relative;
    }
    .barang-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Card Body */
    .barang-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .barang-kode {
        font-size: 11px;
        color: #64748b;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 6px;
    }
    .barang-nama {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    /* Badges */
    .barang-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 16px;
        margin-top: auto;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .badge-blue { background: #e0e7ff; color: #3730a3; }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-yellow { background: #fef9c3; color: #854d0e; }
    .badge-red { background: #fee2e2; color: #991b1b; }
    .badge-gray { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Stok Info Footer */
    .stok-info {
        font-size: 13px;
        color: #64748b;
        padding-top: 12px;
        border-top: 1px dashed #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .stok-info strong {
        color: #0f172a;
        font-weight: 700;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        color: #64748b;
        box-shadow: 0 4px 16px rgba(0,0,0,0.02);
    }
    .empty-state .icon {
        font-size: 48px;
        margin-bottom: 12px;
    }
    .empty-state p {
        margin: 0;
        font-weight: 600;
        font-size: 15px;
        color: #475569;
    }

    /* Pagination Wrapper */
    .pagination-wrap {
        margin-top: 32px;
    }
    .pagination-wrap nav {
        display: flex;
        justify-content: center;
    }
    .pagination-wrap nav > div:first-child { display: none; }
    .pagination-wrap nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pagination-wrap nav a,
    .pagination-wrap nav span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .pagination-wrap nav a:hover {
        background: #f0f7ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }
    .pagination-wrap nav span[aria-current="page"] {
        border-color: #2563eb;
        background: #2563eb;
        color: #fff;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }
    .pagination-wrap nav svg {
        display: block;
        width: 16px;
        height: 16px;
    }

    @media (max-width: 640px) {
        .page-header { flex-direction: column; align-items: flex-start; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar input[type="text"], .filter-bar select { width: 100%; }
        .btn { width: 100%; }
    }
</style>

<div class="barang-page">
    <div class="page-header">
        <div class="header-title-group">
            <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                Kembali
            </a>
            <h1 class="page-title">Barang Gudang</h1>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <a href="{{ route('barang.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:16px; height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Barang
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="filter-card">
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
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                Cari
            </button>
            @if(request()->hasAny(['q','kategori_id','rak_id']))
                <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Reset
                </a>
            @endif
        </form>
    </div>

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
                        <span>Stok: <strong>{{ $barang->stok }} {{ $barang->satuan }}</strong></span>
                        @if($barang->stok_minimum > 0)
                            <span style="color:#94a3b8; font-size:11px;">min {{ $barang->stok_minimum }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pagination-wrap">
            {{ $barangs->links() }}
        </div>
    @endif
</div>

<script>
document.querySelectorAll('.barang-card').forEach(card => {
    card.addEventListener('click', function() {
        window.location.href = this.dataset.url;
    });
});
</script>
@endsection
