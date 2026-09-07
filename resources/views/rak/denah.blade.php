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
        .warehouse-page { max-width:1120px; margin:0 auto; padding:8px 0 34px; }
        .warehouse-header { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; margin-bottom:20px; }
        .warehouse-eyebrow { color:#64748b; font-size:11px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; }
        .warehouse-header h1 { margin:6px 0 4px; color:#123b82; font-size:31px; }
        .warehouse-header p { margin:0; color:#64748b; font-size:12px; }
        .warehouse-search { display:flex; gap:8px; width:min(430px,100%); }
        .warehouse-search input, .warehouse-search select { height:42px; border:1px solid #dbe4f0; border-radius:9px; background:#fff; color:#334e70; font-size:12px; padding:0 12px; }
        .warehouse-search input { flex:1; min-width:0; }
        .warehouse-search select { width:145px; }
        .warehouse-search button { height:42px; padding:0 16px; border:0; border-radius:9px; background:#164194; color:#fff; font-size:12px; font-weight:800; cursor:pointer; }
        .warehouse-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:18px; }
        .summary-item { display:flex; align-items:center; gap:10px; padding:12px 14px; background:#fff; border:1px solid #e2e8f0; border-radius:11px; }
        .summary-dot { width:10px; height:10px; border-radius:50%; flex:0 0 auto; }
        .summary-item strong { display:block; color:#1e3a6f; font-size:17px; }
        .summary-item span { color:#64748b; font-size:10px; }
        .warehouse-layout { display:grid; grid-template-columns:minmax(0,1.55fr) minmax(275px,.75fr); gap:16px; align-items:start; }
        .map-panel, .detail-panel { background:#fff; border:1px solid #e2e8f0; border-radius:15px; box-shadow:0 6px 18px rgba(15,23,42,.04); }
        .map-panel { padding:18px; }
        .panel-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; }
        .panel-heading h2 { margin:0; color:#1e3a6f; font-size:15px; }
        .panel-heading span { color:#64748b; font-size:11px; }
        .warehouse-grid { display:grid; grid-template-columns:repeat(7,minmax(58px,1fr)); gap:9px; padding:4px 0 18px; border-bottom:1px solid #eef2f7; }
        .rack-tile { min-height:80px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; border:1px solid; border-radius:9px; color:inherit; text-align:center; transition:transform .15s ease, box-shadow .15s ease; }
        .rack-tile:hover, .rack-tile.selected { transform:translateY(-2px); box-shadow:0 5px 12px rgba(15,23,42,.12); }
        .rack-tile.aman { border-color:#bbf7d0; background:#f0fdf4; color:#166534; }
        .rack-tile.menipis { border-color:#fed7aa; background:#fff7ed; color:#c2410c; }
        .rack-tile.kosong { border-color:#fecaca; background:#fef2f2; color:#b91c1c; }
        .rack-tile.selected { outline:2px solid #164194; outline-offset:2px; }
        .rack-tile small { font-size:9px; font-weight:700; opacity:.75; }
        .rack-tile strong { font-size:12px; }
        .rack-tile em { font-size:9px; font-style:normal; }
        .map-legend { display:flex; flex-wrap:wrap; gap:14px; padding-top:14px; color:#64748b; font-size:10px; }
        .legend-item { display:flex; align-items:center; gap:6px; }
        .legend-item i { width:9px; height:9px; border-radius:50%; }
        .legend-item i.aman { background:#15803d; }
        .legend-item i.menipis { background:#c2410c; }
        .legend-item i.kosong { background:#dc2626; }
        .detail-panel { padding:18px; position:sticky; top:18px; }
        .detail-kicker { color:#64748b; font-size:10px; font-weight:800; letter-spacing:1px; text-transform:uppercase; }
        .detail-title { display:flex; align-items:center; justify-content:space-between; gap:10px; margin:7px 0 16px; }
        .detail-title h2 { margin:0; color:#123b82; font-size:22px; }
        .status-pill { padding:5px 8px; border-radius:999px; font-size:10px; font-weight:800; }
        .status-pill.aman { background:#dcfce7; color:#166534; }
        .status-pill.menipis { background:#ffedd5; color:#c2410c; }
        .status-pill.kosong { background:#fee2e2; color:#b91c1c; }
        .detail-list { border-top:1px solid #eef2f7; border-bottom:1px solid #eef2f7; }
        .detail-row { display:flex; justify-content:space-between; gap:12px; padding:11px 0; color:#64748b; font-size:11px; }
        .detail-row + .detail-row { border-top:1px solid #f1f5f9; }
        .detail-row strong { color:#1e3a6f; text-align:right; }
        .category-title { margin:16px 0 8px; color:#1e3a6f; font-size:12px; }
        .category-row { display:flex; justify-content:space-between; gap:10px; padding:7px 0; color:#64748b; font-size:11px; }
        .category-row strong { color:#1e3a6f; }
        .assign-form { margin-top:16px; padding-top:16px; border-top:1px solid #eef2f7; }
        .assign-form label { display:block; margin-bottom:7px; color:#1e3a6f; font-size:11px; font-weight:800; }
        .assign-row { display:flex; gap:7px; }
        .assign-row select { min-width:0; flex:1; padding:9px 8px; border:1px solid #dbe4f0; border-radius:8px; color:#334e70; font-size:11px; }
        .assign-row button { padding:9px 11px; border:0; border-radius:8px; background:#164194; color:#fff; font-size:11px; font-weight:800; cursor:pointer; }
        .assign-row select:focus { outline:0; border-color:#2563eb; }
        .empty-map { padding:34px 12px; color:#64748b; text-align:center; font-size:12px; }
        .warehouse-footer-link { display:inline-block; margin-top:14px; color:#2563eb; font-size:11px; font-weight:800; }
        @media (max-width:800px) { .warehouse-header { display:block; } .warehouse-search { margin-top:16px; } .warehouse-layout { grid-template-columns:1fr; } .detail-panel { position:static; } }
        @media (max-width:560px) { .warehouse-page { padding-top:0; } .warehouse-summary { grid-template-columns:repeat(2,1fr); } .warehouse-grid { grid-template-columns:repeat(4,minmax(54px,1fr)); gap:7px; } .rack-tile { min-height:72px; } .warehouse-search { display:grid; grid-template-columns:1fr auto; } .warehouse-search input { grid-column:1 / -1; } .warehouse-search select { width:auto; } }
    </style>

    <div class="warehouse-page">
        @if(auth()->user()->isPimpinan())
            <a class="back-dashboard" href="{{ route('dashboard.pimpinan') }}">&larr; Kembali</a>
        @elseif(auth()->user()->isStaff())
            <a class="back-dashboard" href="{{ route('dashboard.staff') }}">&larr; Kembali</a>
        @elseif(auth()->user()->isAdmin())
            <a class="back-dashboard" href="{{ route('dashboard.admin') }}">&larr; Kembali</a>
        @endif
        <div class="warehouse-header">
            <div>
                <div class="warehouse-eyebrow">Monitoring lokasi barang</div>
                <h1>Denah Gudang</h1>
                <p>Pantau kondisi stok dan temukan lokasi rak secara cepat.</p>
            </div>
            <form class="warehouse-search" method="GET" action="{{ route('denah-gudang') }}">
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari nomor atau lokasi rak..." aria-label="Cari nomor atau lokasi rak">
                <select name="status" aria-label="Filter kondisi stok">
                    <option value="semua" @selected($statusFilter === 'semua')>Semua kondisi</option>
                    <option value="aman" @selected($statusFilter === 'aman')>Stok aman</option>
                    <option value="menipis" @selected($statusFilter === 'menipis')>Stok menipis</option>
                    <option value="kosong" @selected($statusFilter === 'kosong')>Rak kosong</option>
                </select>
                <button type="submit">Terapkan</button>
            </form>
        </div>

        <div class="warehouse-summary">
            <div class="summary-item"><i class="summary-dot" style="background:#164194;"></i><div><strong>{{ $ringkasan['total'] }}</strong><span>Total Rak</span></div></div>
            <div class="summary-item"><i class="summary-dot" style="background:#16a34a;"></i><div><strong>{{ $ringkasan['aman'] }}</strong><span>Stok Aman</span></div></div>
            <div class="summary-item"><i class="summary-dot" style="background:#f97316;"></i><div><strong>{{ $ringkasan['menipis'] }}</strong><span>Stok Menipis</span></div></div>
            <div class="summary-item"><i class="summary-dot" style="background:#dc2626;"></i><div><strong>{{ $ringkasan['kosong'] }}</strong><span>Rak Kosong</span></div></div>
        </div>

        <div class="warehouse-layout">
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
                            <div class="assign-row"><select id="barang_id" name="barang_id" required><option value="">Pilih barang</option>@foreach($barangTersedia as $barang)<option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama }}</option>@endforeach</select><button type="submit">Simpan</button></div>
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
                    <h2 style="margin:8px 0;color:#123b82;font-size:20px;">Belum ada rak</h2>
                    <p style="margin:0;color:#64748b;font-size:12px;">Data rak akan muncul di sini setelah ditambahkan.</p>
                @endif
            </aside>
        </div>
    </div>
@endsection
