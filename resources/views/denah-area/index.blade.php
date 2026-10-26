@extends('layouts.app')

@section('content')
<style>
.page{max-width:1120px;margin:0 auto}
.head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.head-main{display:flex;align-items:center;gap:12px;min-width:0}
.head h1{margin:0;font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
.head p{margin:6px 0 0;font-size:12px;color:var(--andon-muted)}
.head-action{display:inline-flex;align-items:center;gap:8px;white-space:nowrap}
.alert{padding:11px 14px;border-radius:12px;margin-bottom:16px;font-size:13px;font-weight:600;border:1px solid transparent}
.alert-success{background:#ECFDF5;color:#065F46;border-color:#A7F3D0}
.tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}
.tab{padding:8px 14px;border-radius:999px;font-size:12px;font-weight:800;letter-spacing:.02em;border:1px solid var(--andon-line);background:var(--andon-panel);color:var(--andon-muted);text-decoration:none;text-align:center;white-space:nowrap}
.tab--active{background:var(--andon-ink);color:#fff;border-color:var(--andon-ink)}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start}
.card{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);overflow:hidden;position:relative}
.card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
.card-head{padding:14px 16px 12px;border-bottom:1px solid var(--andon-line);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.card-head h2{margin:0;font-size:13px;font-weight:800;letter-spacing:-.02em;color:var(--andon-ink)}
.card-head span{font-size:11px;font-weight:700;color:var(--andon-faint)}
.denah-wrap{padding:16px}
.denah-wrap img{width:100%;height:auto;display:block;border-radius:14px;border:1px solid var(--andon-line);background:#F8FAFC}
.empty{padding:28px 16px;text-align:center;color:var(--andon-faint);font-size:12px;font-weight:600}
.empty .icon{font-size:32px;margin-bottom:8px}
.upload{padding:12px 16px 16px;border-top:1px solid #F1F5F9;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.upload .andon-control{flex:1;min-width:180px}
.rak-grid{padding:12px 16px 16px;display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px}
.rak-card{border:1px solid var(--andon-line);border-radius:14px;padding:12px;background:#F8FAFC}
.rak-card strong{font-size:13px;color:var(--andon-ink)}
.rak-card small{font-size:11px;color:var(--andon-muted);display:block;margin-top:4px}
.rak-card .cnt{margin-top:8px;font-size:11px;font-weight:800;color:var(--andon-ink)}
.pill-row{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700;border:1px solid transparent}
.badge-gray{background:#F8FAFC;color:var(--andon-muted);border-color:var(--andon-line)}
.modal-overlay{display:none;position:fixed;inset:0;padding:12px;background:rgba(15,23,42,.45);z-index:100;align-items:center;justify-content:center}
.modal-overlay.active{display:flex}
.modal{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;padding:28px;width:100%;max-width:480px;max-height:calc(100dvh - 24px);overflow-y:auto;box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05);position:relative}
.modal::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-amber)}
.modal h3{margin:0 0 16px;font-size:17px;font-weight:800;letter-spacing:-.02em;color:var(--andon-ink)}
.modal .form-group{margin-bottom:14px}
.modal label{display:block;margin-bottom:6px;color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.10em;text-transform:uppercase}
.modal .form-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:20px}
@media(max-width:760px){.grid{grid-template-columns:1fr}.rak-grid{grid-template-columns:1fr 1fr}}
@media(max-width:560px){
    .head{align-items:stretch}.head-action{width:100%;justify-content:center}
    .tabs{display:grid;grid-template-columns:repeat(3,1fr);gap:6px}
    .tab{width:100%;padding:8px 6px;font-size:11px;white-space:normal;line-height:1.2}
}
@media(max-width:420px){.rak-grid{grid-template-columns:1fr}.upload{flex-direction:column;align-items:stretch}.upload .andon-control{min-width:0}}
</style>
<div class="page">
    <div class="head">
        <div class="head-main">
            <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="back-link" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
            <div>
                <h1>Denah Gudang</h1>
            </div>
        </div>
        @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn--primary head-action" onclick="document.getElementById('modal-tambah-ruangan').classList.add('active')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Ruangan
            </button>
        @endif
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="tabs" role="tablist">
        @foreach($areas as $a)
            <a href="{{ route('denah-area.show', $a) }}" class="tab">{{ $a->nama }}</a>
        @endforeach
    </div>

    <div class="grid">
        @foreach($areas as $area)
        <div class="card">
            <div class="card-head">
                <h2>{{ $area->kode_area }} — {{ $area->nama }}</h2>
                <span>{{ $area->rak->count() }} rak · {{ $area->barang->count() }} barang</span>
            </div>
            <div class="denah-wrap">
                @if($area->denah_image)
                    <img src="{{ Storage::url($area->denah_image) }}" alt="Denah {{ $area->nama }}" loading="lazy">
                @else
                    <div class="empty"><div class="icon">🗺️</div>Belum ada denah<br><span style="font-weight:500;color:var(--andon-faint)">Upload gambar denah ruangan ini di bawah</span></div>
                @endif
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <form class="upload" method="POST" action="{{ route('denah-area.upload', $area) }}" enctype="multipart/form-data">
                @csrf
                <label for="denah_image-{{ $area->id }}" style="position:absolute;left:-9999px">Gambar denah {{ $area->nama }}</label>
                <input class="andon-control" type="file" name="denah_image" id="denah_image-{{ $area->id }}" accept="image/jpeg,image/png,image/webp" required aria-label="Gambar denah {{ $area->nama }}">
                <button type="submit" class="btn btn--primary" style="min-height:44px">Upload</button>
                @if($area->denah_image)
                    <button type="submit" formaction="{{ route('denah-area.hapus-denah', $area) }}" formmethod="POST" onclick="event.preventDefault(); if(confirm('Hapus denah ini?')){ var f=document.createElement('form'); f.method='POST'; f.action='{{ route('denah-area.hapus-denah', $area) }}'; var c=document.createElement('input'); c.type='hidden'; c.name='_token'; c.value='{{ csrf_token() }}'; var m=document.createElement('input'); m.type='hidden'; m.name='_method'; m.value='DELETE'; f.appendChild(c); f.appendChild(m); document.body.appendChild(f); f.submit(); }" class="btn btn--ghost" style="min-height:44px">Hapus</button>
                @endif
            </form>
            @endif
            <div style="padding:0 16px 6px;font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--andon-muted)">RAK DI RUANGAN INI</div>
            @if($area->rak->isEmpty())
                <div class="empty" style="padding:14px 16px 18px">Belum ada rak di ruangan ini.</div>
            @else
                <div class="rak-grid">
                    @foreach($area->rak->sortBy('kode_rak') as $rak)
                        <a href="{{ route('rak.show', $rak) }}" class="rak-card" style="text-decoration:none">
                            <strong>{{ $rak->kode_rak }}</strong>
                            <small>{{ $rak->nama_lokasi }}</small>
                            <div class="cnt">{{ $rak->barang->count() }} barang</div>
                        </a>
                    @endforeach
                </div>
            @endif
            <div style="padding:10px 16px 14px"><a href="{{ route('denah-area.show', $area) }}" class="btn btn--ghost" style="width:100%">Lihat detail</a></div>
        </div>
        @endforeach
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="modal-overlay" id="modal-tambah-ruangan">
    <div class="modal">
        <h3>Tambah Ruangan</h3>
        <form method="POST" action="{{ route('denah-area.store') }}">
            @csrf
            <div class="form-group">
                <label for="tambah-kode-area">Kode Ruangan</label>
                <input class="andon-control" type="text" name="kode_area" id="tambah-kode-area" placeholder="Contoh: GD-A1" required maxlength="20">
            </div>
            <div class="form-group">
                <label for="tambah-nama-area">Nama Ruangan</label>
                <input class="andon-control" type="text" name="nama" id="tambah-nama-area" placeholder="Contoh: Ruang ATK" required maxlength="100">
            </div>
            <div class="form-group">
                <label for="tambah-keterangan-area">Keterangan</label>
                <textarea class="andon-control" name="keterangan" id="tambah-keterangan-area" rows="3" placeholder="Opsional"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn--ghost" onclick="document.getElementById('modal-tambah-ruangan').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
});
</script>
@endif
@endsection
