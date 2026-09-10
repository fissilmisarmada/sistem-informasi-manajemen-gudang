@extends('layouts.app')

@section('content')
    @php
        $statusLabels = ['aman' => 'Stok Aman', 'menipis' => 'Stok Menipis', 'kosong' => 'Rak Kosong'];
        $selectedStatus = $selectedRak ? $getStatus($selectedRak) : null;
        $kategoriRak = $selectedRak
            ? $selectedRak->barang->groupBy(fn ($barang) => $barang->kategori?->nama ?: 'Tanpa kategori')->map(fn ($barang) => $barang->sum('stok'))->sortDesc()->take(3)
            : collect();
    @endphp

    <style>
        .warehouse-page{max-width:1120px;margin:0 auto}
        .warehouse-header{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;flex-wrap:wrap;margin-bottom:18px}
        .warehouse-eyebrow{color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
        .warehouse-header h1{margin:6px 0 4px;color:var(--andon-ink);font-size:22px;font-weight:800;letter-spacing:-.03em;line-height:1}
        .warehouse-header p{margin:0;color:var(--andon-muted);font-size:12px}
        .warehouse-search{display:flex;gap:8px;width:min(430px,100%)}
        .andon-control{height:42px;border:1px solid #E8EAF0;border-radius:14px;background:#FBFBFD;color:var(--andon-ink);font-size:12px;padding:0 12px;outline:none;transition:border-color .18s,box-shadow .18s,background .18s}
        .andon-control:focus{border-color:var(--andon-ink);box-shadow:0 0 0 3px rgba(15,23,42,.06);background:#fff}
        .warehouse-search input{flex:1;min-width:0}
        .warehouse-search select{width:145px}
        .warehouse-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px}
        .summary-item{display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);transition:box-shadow .22s cubic-bezier(.16,1,.3,1)}
        .summary-item:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
        .summary-dot{width:10px;height:10px;border-radius:50%;flex:0 0 auto}
        .summary-item strong{display:block;color:var(--andon-ink);font-size:17px;letter-spacing:-.02em}
        .summary-item span{color:var(--andon-muted);font-size:10px;font-weight:700}
        .warehouse-layout{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(275px,.75fr);gap:16px;align-items:start}
        .map-panel,.detail-panel{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);position:relative;overflow:hidden;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
        .map-panel:hover,.detail-panel:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
        .map-panel::before,.detail-panel::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
        .map-panel{padding:18px}
        .detail-panel{padding:18px;position:sticky;top:18px}
        .panel-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}
        .panel-heading h2{margin:0;color:var(--andon-ink);font-size:13px;font-weight:800;letter-spacing:-.02em}
        .panel-heading span{color:var(--andon-faint);font-size:11px;font-weight:700}
        .warehouse-grid{display:grid;grid-template-columns:repeat(7,minmax(58px,1fr));gap:9px;padding:4px 0 18px;border-bottom:1px solid var(--andon-line)}
        .rack-tile{min-height:80px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;border:1px solid;border-radius:12px;color:inherit;text-align:center;transition:transform .15s ease,box-shadow .15s ease}
        .rack-tile:hover,.rack-tile.selected{transform:translateY(-2px);box-shadow:0 5px 12px rgba(15,23,42,.12)}
        .rack-tile.aman{border-color:#BBF7D0;background:#F0FDF4;color:#166534}
        .rack-tile.menipis{border-color:#FED7AA;background:#FFF7ED;color:#9A3412}
        .rack-tile.kosong{border-color:#FECACA;background:#FEF2F2;color:#991B1B}
        .rack-tile.selected{outline:2px solid var(--andon-navy);outline-offset:2px}
        .rack-tile small{font-size:9px;font-weight:700;opacity:.75}
        .rack-tile strong{font-size:12px}
        .rack-tile em{font-size:9px;font-style:normal}
        .map-legend{display:flex;flex-wrap:wrap;gap:14px;padding-top:14px;color:var(--andon-muted);font-size:10px;font-weight:700}
        .legend-item{display:flex;align-items:center;gap:6px}
        .legend-item i{width:9px;height:9px;border-radius:50%}
        .legend-item i.aman{background:var(--andon-green)}
        .legend-item i.menipis{background:#EA580C}
        .legend-item i.kosong{background:var(--andon-red)}
        .detail-kicker{color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}
        .detail-title{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:7px 0 16px}
        .detail-title h2{margin:0;color:var(--andon-ink);font-size:22px;letter-spacing:-.02em}
        .status-pill{padding:5px 10px;border-radius:999px;font-size:10px;font-weight:800;border:1px solid transparent}
        .status-pill.aman{background:#DCFCE7;color:#166534;border-color:#BBF7D0}
        .status-pill.menipis{background:#FFEDD5;color:#9A3412;border-color:#FED7AA}
        .status-pill.kosong{background:#FEE2E2;color:#991B1B;border-color:#FECACA}
        .detail-list{border-top:1px solid var(--andon-line);border-bottom:1px solid var(--andon-line)}
        .detail-row{display:flex;justify-content:space-between;gap:12px;padding:11px 0;color:var(--andon-muted);font-size:11px;font-weight:600}
        .detail-row+.detail-row{border-top:1px solid #F1F5F9}
        .detail-row strong{color:var(--andon-ink);text-align:right}
        .category-title{margin:16px 0 8px;color:var(--andon-ink);font-size:12px;font-weight:800}
        .category-row{display:flex;justify-content:space-between;gap:10px;padding:7px 0;color:var(--andon-muted);font-size:11px;font-weight:600}
        .category-row strong{color:var(--andon-ink)}
        .assign-form{margin-top:16px;padding-top:16px;border-top:1px solid var(--andon-line)}
        .assign-form label{display:block;margin-bottom:7px;color:var(--andon-ink);font-size:11px;font-weight:800}
        .assign-row{display:flex;gap:7px}
        .assign-row select{min-width:0;flex:1;padding:9px 10px;border:1px solid #E8EAF0;border-radius:14px;color:var(--andon-ink);font-size:11px;background:#FBFBFD;outline:none}
        .assign-row select:focus{border-color:var(--andon-ink);box-shadow:0 0 0 3px rgba(15,23,42,.06);background:#fff}
        .empty-map{padding:34px 12px;color:var(--andon-muted);text-align:center;font-size:12px;font-weight:600}
        .warehouse-footer-link{display:inline-block;margin-top:14px;color:var(--andon-navy);font-size:11px;font-weight:800}
        .warehouse-footer-link:hover{color:var(--andon-ink);text-decoration:underline}
        .tab-row{display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap}
        .denah-canvas{position:relative;width:100%;height:420px;background:#F8FAFC;border:1px solid #EDEEF2;border-radius:20px;overflow:hidden;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04)}
        @media(max-width:800px){.warehouse-header{display:block} .warehouse-search{margin-top:16px} .warehouse-layout{grid-template-columns:1fr} .detail-panel{position:static}}
        @media(max-width:560px){.warehouse-page{padding-top:0} .warehouse-summary{grid-template-columns:repeat(2,1fr)} .warehouse-grid{grid-template-columns:repeat(4,minmax(54px,1fr));gap:7px} .rack-tile{min-height:72px} .warehouse-search{display:grid;grid-template-columns:1fr auto} .warehouse-search input{grid-column:1/-1} .warehouse-search select{width:auto}}
    </style>

    <div class="warehouse-page">
        @if(auth()->user()->isPimpinan())
            <a class="btn btn--ghost" href="{{ route('dashboard.pimpinan') }}" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">&larr; Kembali</a>
        @elseif(auth()->user()->isStaff())
            <a class="btn btn--ghost" href="{{ route('dashboard.staff') }}" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">&larr; Kembali</a>
        @elseif(auth()->user()->isAdmin())
            <a class="btn btn--ghost" href="{{ route('dashboard.admin') }}" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">&larr; Kembali</a>
        @endif
        <div class="warehouse-header">
            <div>
                <div class="warehouse-eyebrow">Monitoring lokasi barang</div>
                <h1>Denah Gudang</h1>
                <p>Pantau kondisi stok dan temukan lokasi rak secara cepat.</p>
            </div>
            <form class="warehouse-search" method="GET" action="{{ route('denah-gudang') }}">
                <input class="andon-control" type="search" name="q" value="{{ $search }}" placeholder="Cari nomor atau lokasi rak..." aria-label="Cari nomor atau lokasi rak">
                <select class="andon-control" name="status" aria-label="Filter kondisi stok">
                    <option value="semua" @selected($statusFilter === 'semua')>Semua kondisi</option>
                    <option value="aman" @selected($statusFilter === 'aman')>Stok aman</option>
                    <option value="menipis" @selected($statusFilter === 'menipis')>Stok menipis</option>
                    <option value="kosong" @selected($statusFilter === 'kosong')>Rak kosong</option>
                </select>
                <button type="submit" class="btn btn--primary" style="height:42px">Terapkan</button>
            </form>
        </div>

        <div class="warehouse-summary">
            <div class="summary-item"><i class="summary-dot" style="background:var(--andon-navy);"></i><div><strong>{{ $ringkasan['total'] }}</strong><span>Total Rak</span></div></div>
            <div class="summary-item"><i class="summary-dot" style="background:var(--andon-green);"></i><div><strong>{{ $ringkasan['aman'] }}</strong><span>Stok Aman</span></div></div>
            <div class="summary-item"><i class="summary-dot" style="background:#EA580C;"></i><div><strong>{{ $ringkasan['menipis'] }}</strong><span>Stok Menipis</span></div></div>
            <div class="summary-item"><i class="summary-dot" style="background:var(--andon-red);"></i><div><strong>{{ $ringkasan['kosong'] }}</strong><span>Rak Kosong</span></div></div>
        </div>

        <div class="tab-row">
            <button type="button" id="tab-rak" class="btn btn--primary" style="min-height:36px;padding:0 16px;font-size:12px;" onclick="showTab('rak')">Rak (Buku)</button>
            <button type="button" id="tab-area" class="btn btn--ghost" style="min-height:36px;padding:0 16px;font-size:12px;" onclick="showTab('area')">Area Gudang (Buku/Barang Luar Rak)</button>
        </div>

        <div class="warehouse-layout" id="panel-rak">
            <section class="map-panel">
                <div class="panel-heading"><h2>Posisi Rak Gudang</h2><span>{{ $dataRak->count() }} rak ditampilkan</span></div>
                @if($dataRak->isEmpty())
                    <div class="empty-map">Tidak ada rak yang sesuai dengan pencarian atau filter.</div>
                @else
                    <div class="warehouse-grid">
                        @foreach($dataRak as $rak)
                            @php $rakStatus = $getStatus($rak); @endphp
                            <a class="rack-tile {{ $rakStatus }} {{ $selectedRak?->id === $rak->id ? 'selected' : '' }}" href="{{ route('denah-gudang', ['q' => $search, 'status' => $statusFilter, 'rak' => $rak->id]) }}" aria-label="Lihat detail {{ $rak->kode_rak }}">
                                <small>{{ $rak->kode_rak }}</small>
                                <strong>{{ number_format((int) ($rak->barang_sum_stok ?? 0), 0, ',', '.') }}</strong>
                                <em>Total stok</em>
                            </a>
                        @endforeach
                    </div>
                @endif
                <div class="map-legend">
                    @foreach($statusLabels as $status => $label)
                        <span class="legend-item"><i class="{{ $status }}"></i>{{ $label }}</span>
                    @endforeach
                </div>
                @if(!auth()->user()->isPimpinan())
                    <a class="warehouse-footer-link" href="{{ route('rak.index', ['from' => 'denah']) }}">Kelola data rak &rsaquo;</a>
                @endif
            </section>

            <aside class="detail-panel">
                @if($selectedRak)
                    <div class="detail-kicker">Informasi Rak</div>
                    <div class="detail-title"><h2>{{ $selectedRak->kode_rak }}</h2><span class="status-pill {{ $selectedStatus }}">{{ $statusLabels[$selectedStatus] }}</span></div>
                    <div class="detail-list">
                        <div class="detail-row"><span>Lokasi</span><strong>{{ $selectedRak->nama_lokasi }}</strong></div>
                        <div class="detail-row"><span>Jumlah Barang</span><strong>{{ number_format((int) ($selectedRak->barang_sum_stok ?? 0), 0, ',', '.') }} unit</strong></div>
                        <div class="detail-row"><span>Jenis Barang</span><strong>{{ $selectedRak->barang->count() }}</strong></div>
                        <div class="detail-row"><span>Kapasitas</span><strong>{{ $selectedRak->kapasitas ? number_format($selectedRak->kapasitas, 0, ',', '.') . ' unit' : 'Belum ditentukan' }}</strong></div>
                    </div>
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <form method="POST" action="{{ route('denah-gudang.assign') }}" class="assign-form">
                            @csrf
                            <input type="hidden" name="rak_id" value="{{ $selectedRak->id }}">
                            <label for="barang_id">Tempatkan barang ke rak ini</label>
                            <div class="assign-row"><select id="barang_id" name="barang_id" required><option value="">Pilih barang</option>@foreach($barangTersedia as $barang)<option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama }}</option>@endforeach</select><button type="submit" class="btn btn--primary" style="min-height:38px;white-space:nowrap">Simpan</button></div>
                        </form>
                    @endif
                    <h3 class="category-title">Kategori Barang</h3>
                    @forelse($kategoriRak as $kategori => $jumlah)
                        <div class="category-row"><span>{{ $kategori }}</span><strong>{{ number_format($jumlah, 0, ',', '.') }} unit</strong></div>
                    @empty
                        <div class="category-row"><span>Belum ada barang di rak ini.</span></div>
                    @endforelse
                    <a class="warehouse-footer-link" href="{{ route('rak.show', ['rak' => $selectedRak, 'from' => 'denah']) }}">Lihat isi rak &rsaquo;</a>
                @else
                    <div class="detail-kicker">Informasi Rak</div>
                    <h2 style="margin:8px 0;color:var(--andon-ink);font-size:20px;letter-spacing:-.02em">Belum ada rak</h2>
                    <p style="margin:0;color:var(--andon-muted);font-size:12px;">Data rak akan muncul di sini setelah ditambahkan.</p>
                @endif
            </aside>
        </div>

        <div id="panel-area" style="display:none;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin:18px 0 10px;gap:10px;flex-wrap:wrap;">
                <h2 style="margin:0;color:var(--andon-ink);font-size:17px;font-weight:800;letter-spacing:-.02em">Denah Area Bebas</h2>
                @if(!auth()->user()->isPimpinan())
                <button type="button" id="btn-tambah-area" class="btn btn--primary" style="min-height:36px">+ Tambah Area</button>
                @endif
            </div>
            <div id="denah-canvas" class="denah-canvas">
                @foreach($denahAreas as $area)
                <div class="denah-box" data-id="{{ $area->id }}" data-x="{{ $area->x }}" data-y="{{ $area->y }}" data-w="{{ $area->w }}" data-h="{{ $area->h }}" onclick="openAreaDetail({{ $area->id }})" title="Klik untuk lihat isi" style="position:absolute;left:{{ $area->x }}%;top:{{ $area->y }}%;width:{{ $area->w }}%;height:{{ $area->h }}%;background:{{ $area->warna }}18;border:2px solid {{ $area->warna }};border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;user-select:none;">
                    <strong style="color:{{ $area->warna }};font-size:12px;">{{ $area->kode_area }}</strong>
                    <span style="color:var(--andon-ink);font-size:11px;font-weight:600">{{ $area->nama }} ({{ $area->barang_count }})</span>
                    @if(!auth()->user()->isPimpinan())
                    <span style="margin-top:4px;display:flex;gap:4px;">
                        <button type="button" onclick="event.stopPropagation();editArea({{ $area->id }})" class="btn btn--ghost" style="min-height:24px;padding:0 8px;font-size:10px">Edit</button>
                        <button type="button" onclick="event.stopPropagation();hapusArea({{ $area->id }})" class="btn btn--ghost" style="min-height:24px;padding:0 8px;font-size:10px;color:var(--andon-red);border-color:#FECACA">Hapus</button>
                        <a href="{{ route('denah-area.show', $area) }}" onclick="event.stopPropagation()" class="btn btn--ghost" style="min-height:24px;padding:0 8px;font-size:10px">Lihat</a>
                    </span>
                    @else
                    <a href="{{ route('denah-area.show', $area) }}" onclick="event.stopPropagation()" class="btn btn--ghost" style="margin-top:4px;min-height:24px;padding:0 8px;font-size:10px">Lihat isi</a>
                    @endif
                </div>
                @endforeach
            </div>
            <div id="area-popover-backdrop" style="display:none;position:fixed;inset:0;z-index:40;" onclick="closeAreaPopover()"></div>
            <div id="area-popover" role="dialog" aria-modal="true" style="display:none;position:fixed;z-index:41;max-width:480px;width:min(88vw,480px);background:var(--andon-panel);border:1px solid var(--andon-line);border-radius:16px;box-shadow:var(--andon-shadow-strong);overflow:hidden;opacity:0;transform:scale(.96) translateY(6px);transform-origin:top left;transition:opacity .18s ease, transform .22s cubic-bezier(.16,1,.3,1);">
                <div style="padding:14px 16px 12px;border-bottom:1px solid var(--andon-line);">
                    <div id="area-popover-title" style="color:var(--andon-ink);font-size:14px;font-weight:800;letter-spacing:-.02em;"></div>
                    <div id="area-popover-sub" style="color:var(--andon-muted);font-size:11px;margin-top:2px;"></div>
                </div>
                <div id="area-popover-body" style="padding:10px 14px 6px;max-height:42vh;overflow:auto;font-size:13px;color:var(--andon-ink);">Memuat...</div>
                <div style="padding:10px 14px 12px;border-top:1px solid #F8FAFC;display:flex;justify-content:space-between;align-items:center;gap:10px;">
                    <span style="color:var(--andon-faint);font-size:11px;">Klik di luar untuk tutup</span>
                    <a id="area-popover-link" href="#" style="color:var(--andon-navy);font-weight:700;font-size:12px;text-decoration:none;">Lihat halaman &rsaquo;</a>
                </div>
            </div>
            @if(!auth()->user()->isPimpinan())
            <div style="margin-top:12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <select id="area-barang" class="andon-control" style="flex:1;min-width:180px;height:40px"><option value="">Pilih barang (semua, termasuk buku)</option>@foreach(\App\Models\Barang::with('kategori')->orderBy('nama')->get() as $b)<option value="{{ $b->id }}">{{ $b->kode_barang }} - {{ $b->nama }}{{ $b->rak_id ? ' [Rak: '.$b->rak->kode_rak.']' : '' }}{{ $b->denah_area_id ? ' [Area]' : '' }}</option>@endforeach</select>
                <select id="area-target" class="andon-control" style="flex:1;min-width:140px;height:40px"><option value="">Pilih area</option>@foreach($denahAreas as $a)<option value="{{ $a->id }}">{{ $a->kode_area }} - {{ $a->nama }}</option>@endforeach</select>
                <button type="button" onclick="assignBarangKeArea()" class="btn btn--primary" style="min-height:40px">Tempatkan</button>
            </div>
            <p style="color:var(--andon-muted);font-size:11px;margin-top:8px;">Tip: drag kotak area untuk atur posisi. Buku/barang apa pun bisa ditaruh di area jika rak penuh.</p>
            @endif
        </div>
    </div>

<script>
const canEdit = {{ auth()->user()->isPimpinan() ? 'false' : 'true' }};
function showTab(t){
    document.getElementById('panel-rak').style.display=t==='rak'?'grid':'none';
    document.getElementById('panel-area').style.display=t==='area'?'block':'none';
    const rk=document.getElementById('tab-rak'), ar=document.getElementById('tab-area');
    if(t==='rak'){ rk.className='btn btn--primary'; ar.className='btn btn--ghost'; }
    else { rk.className='btn btn--ghost'; ar.className='btn btn--primary'; }
    rk.style.cssText='min-height:36px;padding:0 16px;font-size:12px';
    ar.style.cssText='min-height:36px;padding:0 16px;font-size:12px';
}
async function api(url,method,body){ const r=await fetch(url,{method,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:body?JSON.stringify(body):undefined}); if(!r.ok) throw new Error(await r.text()); return r.json(); }
document.getElementById('btn-tambah-area')?.addEventListener('click', async ()=>{ const kode=prompt('Kode area (mis: A1)'); if(!kode) return; const nama=prompt('Nama area'); if(!nama) return; try{ await api('{{ route("denah-area.store") }}','POST',{kode_area:kode,nama,x:5,y:5,w:20,h:15,warna:'#0F2540'}); location.reload(); }catch(e){ alert('Gagal: '+e.message); }});
async function hapusArea(id){ if(!confirm('Hapus area ini?')) return; await api('/denah-area/'+id,'DELETE'); location.reload(); }
async function editArea(id){ const nama=prompt('Nama baru'); if(!nama) return; const kode=prompt('Kode baru'); await api('/denah-area/'+id,'PUT',{kode_area:kode||undefined,nama,x:5,y:5,w:20,h:15}); location.reload(); }
async function assignBarangKeArea(){ const barang_id=document.getElementById('area-barang').value, denah_area_id=document.getElementById('area-target').value; if(!barang_id||!denah_area_id) return alert('Pilih barang & area'); await api('{{ route("denah-area.assign") }}','POST',{barang_id,denah_area_id}); location.reload(); }
let popoverAnchor=null;
function closeAreaPopover(){
    const pop=document.getElementById('area-popover'), bd=document.getElementById('area-popover-backdrop');
    pop.style.opacity='0'; pop.style.transform='scale(.96) translateY(6px)';
    setTimeout(()=>{ pop.style.display='none'; bd.style.display='none'; }, 180);
}
function placePopover(anchor){
    const pop=document.getElementById('area-popover'), bd=document.getElementById('area-popover-backdrop');
    bd.style.display='block'; pop.style.display='block';
    const r=anchor.getBoundingClientRect(), vw=window.innerWidth, vh=window.innerHeight;
    const pw=pop.offsetWidth, ph=pop.offsetHeight;
    let left=Math.min(vw-16-pw, Math.max(16, r.left + r.width/2 - pw/2));
    let top=r.bottom + 10;
    if(top + ph > vh - 16) top = Math.max(16, r.top - ph - 10);
    pop.style.left=left+'px'; pop.style.top=top+'px';
    pop.style.transformOrigin = (top > r.top ? 'top' : 'bottom') + ' center';
    requestAnimationFrame(()=>{ pop.style.opacity='1'; pop.style.transform='scale(1) translateY(0)'; });
}
async function openAreaDetail(id){
    const anchor=event?.currentTarget || document.querySelector(`.denah-box[data-id="${id}"]`);
    popoverAnchor=anchor;
    const title=document.getElementById('area-popover-title'), sub=document.getElementById('area-popover-sub'), body=document.getElementById('area-popover-body'), link=document.getElementById('area-popover-link');
    title.textContent='Memuat...'; sub.textContent=''; body.innerHTML='<span style="color:var(--andon-faint);">Memuat…</span>'; link.href='/denah-area/'+id;
    placePopover(anchor);
    try{
        const data=await api('/denah-area/'+id,'GET');
        title.textContent=data.kode_area+' — '+data.nama;
        sub.textContent=(data.keterangan||'Area gudang')+' · '+data.barang.length+' barang';
        link.href='/denah-area/'+id;
        if(!data.barang || data.barang.length===0) body.innerHTML='<p style="color:var(--andon-muted);padding:8px 0 4px;">Belum ada barang di area ini.</p>';
        else body.innerHTML='<div style="display:flex;flex-direction:column;gap:6px;">'+data.barang.map(b=>`<a href="/barang/${b.id}" style="display:flex;justify-content:space-between;align-items:center;gap:10px;padding:8px 10px;border:1px solid var(--andon-line);border-radius:10px;text-decoration:none;color:inherit;background:#F8FAFC;"><span><strong style="color:var(--andon-ink);font-size:12px;">${b.kode_barang}</strong> <span style="color:var(--andon-ink);">${b.nama}</span> <span style="color:var(--andon-faint);font-size:11px;">· ${b.kategori?b.kategori.nama:'-'}</span></span><strong style="color:var(--andon-ink);white-space:nowrap;">${b.stok} ${b.satuan}</strong></a>`).join('')+'</div>';
        placePopover(anchor);
    }catch(e){ body.textContent='Gagal memuat: '+e.message; }
}
document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeAreaPopover(); });
window.addEventListener('resize', ()=>{ if(popoverAnchor) placePopover(popoverAnchor); });
window.addEventListener('scroll', ()=>{ if(document.getElementById('area-popover').style.display!=='none' && popoverAnchor) placePopover(popoverAnchor); }, true);
// drag
if(canEdit){
let drag=null, moved=false, canvas=document.getElementById('denah-canvas');
canvas?.addEventListener('mousedown', e=>{ const box=e.target.closest('.denah-box'); if(!box) return; if(e.target.closest('button,a')) return; drag={el:box,sx:e.clientX,sy:e.clientY,ox:parseFloat(box.dataset.x),oy:parseFloat(box.dataset.y)}; moved=false; e.preventDefault(); });
window.addEventListener('mousemove', e=>{ if(!drag) return; moved=true; const rect=canvas.getBoundingClientRect(); const dx=(e.clientX-drag.sx)/rect.width*100, dy=(e.clientY-drag.sy)/rect.height*100; let nx=Math.max(0,Math.min(90,drag.ox+dx)), ny=Math.max(0,Math.min(85,drag.oy+dy)); drag.el.style.left=nx+'%'; drag.el.style.top=ny+'%'; drag.el.dataset.x=nx; drag.el.dataset.y=ny; });
window.addEventListener('mouseup', async (e)=>{ if(!drag) return; const id=drag.el.dataset.id, x=parseFloat(drag.el.dataset.x), y=parseFloat(drag.el.dataset.y), w=parseFloat(drag.el.dataset.w), h=parseFloat(drag.el.dataset.h); const wasMoved=moved; drag=null; moved=false; if(wasMoved){ e.stopPropagation(); try{ await api('/denah-area/'+id+'/posisi','PATCH',{x,y,w,h}); }catch{ } }});
window.addEventListener('click', e=>{ if(moved) e.stopPropagation(); }, true);
}
</script>
@endsection
