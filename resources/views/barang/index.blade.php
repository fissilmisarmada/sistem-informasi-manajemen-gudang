@extends('layouts.app')

@section('content')
<style>
    .barang-page{max-width:1120px;margin:0 auto}
    .page-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
    .page-head h1{margin:0;font-size:23px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
    .page-head p{margin:6px 0 0;font-size:12px;color:var(--andon-muted);line-height:1.4}
    .alert{padding:12px 14px;border-radius:12px;margin-bottom:16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px}
    .alert-success{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0}
    .filter-card{background:var(--andon-panel);border:1px solid var(--andon-line);border-radius:16px;padding:14px;box-shadow:var(--andon-shadow);margin-bottom:18px}
    .filter-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .andon-control{padding:10px 12px;border:1px solid var(--andon-line-strong);border-radius:10px;font-size:13px;color:var(--andon-ink);background:#F8FAFC;outline:none;transition:border-color .18s,box-shadow .18s}
    .andon-control:focus{border-color:var(--andon-ink);box-shadow:0 0 0 3px rgba(15,23,42,.12);background:#fff}
    .filter-bar input[type="text"]{flex:2;min-width:200px}
    .filter-bar select{flex:1;min-width:150px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
    .barang-card{background:var(--andon-panel);border:1px solid var(--andon-line);border-radius:16px;overflow:hidden;cursor:pointer;transition:transform .18s,box-shadow .18s,border-color .18s;display:flex;flex-direction:column;position:relative;box-shadow:0 4px 16px rgba(15,23,42,.04)}
    .barang-card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .barang-card--warn::before{background:var(--andon-red)}
    .barang-card:hover{transform:translateY(-3px);box-shadow:var(--andon-shadow);border-color:var(--andon-line-strong)}
    .barang-img{width:100%;height:168px;object-fit:cover;background:#F8FAFC;display:flex;align-items:center;justify-content:center;color:var(--andon-faint);font-size:42px;border-bottom:1px solid var(--andon-line);position:relative}
    .barang-img img{width:100%;height:100%;object-fit:cover;display:block}
    .barang-body{padding:14px 14px 12px;display:flex;flex-direction:column;flex-grow:1}
    .barang-kode{font-size:10px;color:var(--andon-faint);font-weight:800;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px}
    .barang-nama{font-size:14px;font-weight:800;color:var(--andon-ink);margin-bottom:10px;line-height:1.35;letter-spacing:-.02em}
    .barang-meta{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;margin-top:auto}
    .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;border:1px solid transparent}
    .badge-blue{background:#EFF6FF;color:#1E3A5F;border-color:#DBEAFE}
    .badge-green{background:#ECFDF5;color:#065F46;border-color:#A7F3D0}
    .badge-yellow{background:#FEFCE8;color:#854D0E;border-color:#FDE68A}
    .badge-red{background:#FEF2F2;color:#991B1B;border-color:#FECACA}
    .badge-gray{background:#F8FAFC;color:var(--andon-muted);border-color:var(--andon-line)}
    .stok-info{font-size:12px;color:var(--andon-muted);padding-top:10px;border-top:1px dashed var(--andon-line);display:flex;align-items:center;justify-content:space-between;gap:8px}
    .stok-info strong{color:var(--andon-ink);font-weight:800}
    .empty-state{text-align:center;padding:48px 20px;background:var(--andon-panel);border-radius:16px;border:1px solid var(--andon-line);color:var(--andon-muted);box-shadow:var(--andon-shadow)}
    .empty-state .icon{font-size:36px;margin-bottom:8px}
    .empty-state p{margin:0;font-weight:700;font-size:13px;color:var(--andon-muted)}
    .pagination-wrap{margin-top:22px}
    .pagination-wrap nav{display:flex;justify-content:center}
    .pagination-wrap nav>div:first-child{display:none}
    .pagination-wrap nav>div:last-child{display:flex;align-items:center;gap:6px}
    .pagination-wrap nav a,.pagination-wrap nav span{display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border:1px solid var(--andon-line);border-radius:10px;background:var(--andon-panel);color:var(--andon-muted);font-size:13px;font-weight:600;text-decoration:none;transition:all .18s}
    .pagination-wrap nav a:hover{background:#F8FAFC;border-color:var(--andon-line-strong);color:var(--andon-ink)}
    .pagination-wrap nav span[aria-current="page"]{border-color:var(--andon-ink);background:var(--andon-ink);color:#fff;font-weight:700}
    .pagination-wrap nav svg{width:16px;height:16px}
    @media(max-width:640px){
        .page-head{gap:10px;margin-bottom:14px}
        .page-head h1{font-size:20px}
        .filter-card{padding:12px;border-radius:14px;margin-bottom:14px}
        .filter-bar{display:grid;grid-template-columns:1fr 1fr;gap:8px}
        .filter-bar input[type="text"]{grid-column:1/-1;width:100%;min-width:0}
        .filter-bar select{width:100%;min-width:0}
        .filter-bar .btn{width:100%}
        .grid{grid-template-columns:1fr;gap:10px}
        .barang-card{border-radius:14px}
        .barang-img{height:118px;font-size:30px}
        .barang-body{padding:12px}
    }
    @media(max-width:380px){.filter-bar{grid-template-columns:1fr}.filter-bar input[type="text"]{grid-column:auto}}
</style>

<div class="barang-page">
    <div class="page-head">
        <div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn--ghost" style="min-height:36px;padding:0 14px;font-size:12px">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Kembali
                </a>
                <h1>Barang Gudang</h1>
            </div>
            <p>Inventaris lantai — saring, cari, dan buka detail stok.</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <a href="{{ route('barang.create') }}" class="btn btn--primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Barang
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex:0 0 auto"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('barang.index') }}" class="filter-bar">
            <input class="andon-control" type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / kode barang...">
            <select class="andon-control" name="kategori_id">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                @endforeach
            </select>
            <select class="andon-control" name="rak_id">
                <option value="">Semua Rak</option>
                @foreach($raks as $rak)
                    <option value="{{ $rak->id }}" {{ request('rak_id') == $rak->id ? 'selected' : '' }}>{{ $rak->kode_rak }} – {{ $rak->nama_lokasi }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn--primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                Cari
            </button>
            @if(request()->hasAny(['q','kategori_id','rak_id']))
                <a href="{{ route('barang.index') }}" class="btn btn--ghost">Reset</a>
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
            <div class="barang-card {{ $barang->isStokMenipis() ? 'barang-card--warn' : '' }}" data-url="{{ route('barang.show', ['barang' => $barang, 'from' => request('from')]) }}">
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
                            <span style="color:var(--andon-faint);font-size:11px;font-weight:700">min {{ $barang->stok_minimum }}</span>
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
    card.addEventListener('click', function() { window.location.href = this.dataset.url; });
});
</script>
@endsection
