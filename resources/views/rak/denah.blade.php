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
        .denah-link{display:inline-flex;align-items:center;gap:6px;margin-bottom:14px;color:var(--andon-navy);font-size:12px;font-weight:800;text-decoration:none}
        .denah-link:hover{color:var(--andon-ink);text-decoration:underline}
        @media(max-width:800px){.warehouse-header{display:block} .warehouse-search{margin-top:16px} .warehouse-layout{grid-template-columns:1fr} .detail-panel{position:static}}
        @media(max-width:560px){.warehouse-page{padding-top:0} .warehouse-summary{grid-template-columns:repeat(2,1fr)} .warehouse-grid{grid-template-columns:repeat(4,minmax(54px,1fr));gap:7px} .rack-tile{min-height:72px} .warehouse-search{display:grid;grid-template-columns:1fr auto} .warehouse-search input{grid-column:1/-1} .warehouse-search select{width:auto}}
    </style>

    <div class="warehouse-page">
        <div class="warehouse-header">
            <div style="display:flex;align-items:center;gap:12px">
                @if(auth()->user()->isPimpinan())
                    <a class="back-link" href="{{ route('dashboard.pimpinan') }}"  aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
                @elseif(auth()->user()->isStaff())
                    <a class="back-link" href="{{ route('dashboard.staff') }}"  aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
                @elseif(auth()->user()->isAdmin())
                    <a class="back-link" href="{{ route('dashboard.admin') }}"  aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
                @endif
                <div>
                    <div class="warehouse-eyebrow">Monitoring lokasi barang</div>
                    <h1>Denah Gudang</h1>
                    <p>Pantau kondisi stok dan temukan lokasi rak secara cepat.</p>
                </div>
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

        <a href="{{ route('denah-area.index') }}" class="denah-link">Lihat denah ruangan (foto) &rsaquo;</a>

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

    </div>

@endsection
