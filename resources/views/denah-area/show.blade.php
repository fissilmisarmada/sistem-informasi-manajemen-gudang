@extends('layouts.app')

@section('content')
<style>
.detail-page{max-width:1120px;margin:0 auto}
.head{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.head h1{margin:4px 0 0;color:var(--andon-ink);font-size:23px;font-weight:800;letter-spacing:-.03em;line-height:1}
.meta{color:var(--andon-muted);font-size:12px;margin-top:6px}
.kicker{color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
.alert{padding:11px 14px;border-radius:12px;margin-bottom:16px;font-size:13px;font-weight:600;border:1px solid transparent}
.alert-success{background:#ECFDF5;color:#065F46;border-color:#A7F3D0}
.card{overflow:hidden;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);position:relative;margin-bottom:16px}
.card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
.card-head{padding:14px 16px 12px;border-bottom:1px solid var(--andon-line);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.card-head h2{margin:0;color:var(--andon-ink);font-size:13px;font-weight:800;letter-spacing:-.02em}
.card-head span{color:var(--andon-faint);font-size:11px;font-weight:700}
.denah-wrap{padding:16px}
.denah-wrap img{width:100%;height:auto;display:block;border-radius:14px;border:1px solid var(--andon-line);background:#F8FAFC}
.upload{padding:12px 16px 16px;border-top:1px solid #F1F5F9;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.upload .andon-control{flex:1;min-width:180px}
.rak-grid{padding:12px 16px 16px;display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px}
.rak-card{border:1px solid var(--andon-line);border-radius:14px;padding:12px;background:#F8FAFC;text-decoration:none;display:block}
.rak-card strong{font-size:13px;color:var(--andon-ink)}
.rak-card small{font-size:11px;color:var(--andon-muted);display:block;margin-top:4px}
.list{padding:0}
.row{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #F1F5F9;font-size:13px}
.row:last-child{border-bottom:0}
.row a{color:var(--andon-navy);text-decoration:none;font-weight:700}
.row a:hover{color:var(--andon-ink);text-decoration:underline}
.badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#F8FAFC;color:var(--andon-muted);border:1px solid var(--andon-line)}
.empty{padding:32px 16px;text-align:center;color:var(--andon-muted);font-size:13px;font-weight:600}
.tabs{display:flex;gap:8px;flex-wrap:nowrap;overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;scrollbar-width:none;padding-bottom:4px;margin-bottom:16px;scroll-snap-type:x mandatory}
.tabs::-webkit-scrollbar{display:none}
.tab{flex:0 0 auto;white-space:nowrap;scroll-snap-align:start;padding:8px 14px;border-radius:999px;font-size:12px;font-weight:800;letter-spacing:.02em;border:1px solid var(--andon-line);background:var(--andon-panel);color:var(--andon-muted);text-decoration:none}
.tab--active{background:var(--andon-ink);color:#fff;border-color:var(--andon-ink)}
@media(max-width:640px){.rak-grid{grid-template-columns:1fr 1fr}}
@media(max-width:420px){.rak-grid{grid-template-columns:1fr}.upload{flex-direction:column;align-items:stretch}.upload .andon-control{min-width:0}}
</style>
<div class="detail-page">
    <div class="head" style="margin-bottom:12px">
        <a href="{{ route('denah-area.index') }}" class="back-link"  aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
        <h1 style="margin:0;font-size:18px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1">Detail Area</h1>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert" style="background:#FEF2F2;color:#991B1B;border-color:#FECACA">{{ $errors->first() }}</div>@endif
    <div class="tabs">
        @php $all = \App\Models\DenahArea::orderBy('kode_area')->get(); @endphp
        @foreach($all as $t)
            <a href="{{ route('denah-area.show', $t) }}" class="tab {{ $t->id === $area->id ? 'tab--active' : '' }}">{{ $t->nama }}</a>
        @endforeach
    </div>
    <div class="head">
        <div><div class="kicker">Area gudang</div><h1>{{ $area->kode_area }} — {{ $area->nama }}</h1><div class="meta">{{ $area->keterangan ?? 'Area gudang' }} · {{ $area->rak->count() }} rak · {{ $area->barang->count() }} barang</div></div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Denah {{ $area->nama }}</h2><span>1 gambar per ruangan</span></div>
        <div class="denah-wrap">
            @if($area->denah_image)
                <img src="{{ Storage::url($area->denah_image) }}" alt="Denah {{ $area->nama }}">
            @else
                <div class="empty">Belum ada denah — upload gambar denah ruangan ini di bawah.</div>
            @endif
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <form class="upload" method="POST" action="{{ route('denah-area.upload', $area) }}" enctype="multipart/form-data">
            @csrf
            <label for="denah_image-show" style="position:absolute;left:-9999px">Gambar denah {{ $area->nama }}</label>
            <input class="andon-control" type="file" name="denah_image" id="denah_image-show" accept="image/jpeg,image/png,image/webp" required aria-label="Gambar denah {{ $area->nama }}">
            <button type="submit" class="btn btn--primary" style="min-height:44px">Upload denah</button>
            @if($area->denah_image)
                <button type="submit" formaction="{{ route('denah-area.hapus-denah', $area) }}" formmethod="POST" onclick="event.preventDefault(); if(confirm('Hapus denah ini?')){ var f=document.createElement('form'); f.method='POST'; f.action='{{ route('denah-area.hapus-denah', $area) }}'; var c=document.createElement('input'); c.type='hidden'; c.name='_token'; c.value='{{ csrf_token() }}'; var m=document.createElement('input'); m.type='hidden'; m.name='_method'; m.value='DELETE'; f.appendChild(c); f.appendChild(m); document.body.appendChild(f); f.submit(); }" class="btn btn--ghost" style="min-height:44px">Hapus</button>
            @endif
        </form>
        @endif
    </div>

    <div class="card">
        <div class="card-head"><h2>Rak di {{ $area->nama }}</h2><span>{{ $area->rak->count() }} rak</span></div>
        @if($area->rak->isEmpty())
            <div class="empty">Belum ada rak di ruangan ini.</div>
        @else
            <div class="rak-grid">
                @foreach($area->rak->sortBy('kode_rak') as $rak)
                    <a href="{{ route('rak.show', $rak) }}" class="rak-card">
                        <strong>{{ $rak->kode_rak }}</strong>
                        <small>{{ $rak->nama_lokasi }}</small>
                        <small style="margin-top:8px;color:var(--andon-ink);font-weight:800">{{ $rak->barang->count() }} barang</small>
                    </a>
                @endforeach
            </div>
        @endif
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
