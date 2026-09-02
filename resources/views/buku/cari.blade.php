@extends('layouts.app')

@section('content')
    <style>
        .search-page { max-width: 900px; margin: 0 auto; padding: 12px 0 34px; }
        .search-head { display:flex; align-items:center; gap:14px; margin-bottom:20px; }
        .search-head h1 { margin:0; color:#123b82; font-size:30px; }
        .search-subtitle { margin:4px 0 0; color:#64748b; font-size:12px; }
        .mode-switch { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:14px; }
        .mode-button { border:1px solid #dbe4f0; border-radius:10px; background:#fff; padding:13px; color:#1e3a6f; font-weight:800; cursor:pointer; text-align:left; }
        .mode-button.active { background:#eff6ff; border-color:#2563eb; color:#2563eb; }
        .search-form { display:flex; gap:9px; padding:9px; background:#fff; border:1px solid #dbe4f0; border-radius:12px; margin-bottom:16px; }
        .search-form input { flex:1; min-width:0; border:0; outline:0; padding:8px; font-size:13px; }
        .search-submit { border:0; border-radius:9px; background:#2563eb; color:#fff; padding:10px 15px; font-weight:800; cursor:pointer; }
        .scanner-panel { display:none; background:#0f3e8f; border-radius:14px; padding:18px; color:#fff; margin-bottom:16px; }
        .scanner-panel.open { display:block; }
        #barcodeVideo { width:100%; max-height:260px; object-fit:cover; border-radius:10px; background:#0b2554; display:none; margin-top:12px; }
        .result-label { display:flex; justify-content:space-between; color:#1e3a6f; font-size:12px; font-weight:800; margin:20px 0 10px; }
        .result-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:16px; box-shadow:0 7px 20px rgba(15,23,42,.06); }
        .book-summary { display:grid; grid-template-columns:74px 1fr; gap:14px; }
        .book-cover { width:74px; height:100px; border-radius:8px; object-fit:cover; background:linear-gradient(135deg,#dbeafe,#bfdbfe); display:grid; place-items:center; color:#2563eb; font-size:10px; font-weight:800; text-align:center; }
        .book-summary h2 { margin:0 0 5px; color:#1e3a6f; font-size:16px; }
        .book-summary p { margin:3px 0; color:#64748b; font-size:11px; }
        .tags { display:flex; flex-wrap:wrap; gap:5px; margin-top:8px; }
        .tag { padding:4px 7px; border-radius:5px; background:#eff6ff; color:#2563eb; font-size:10px; font-weight:800; }
        .tag.green { background:#dcfce7; color:#15803d; }
        .info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:14px; }
        .info-box { padding:11px; background:#f8fafc; border:1px solid #eef2f7; border-radius:9px; }
        .info-box small { display:block; color:#64748b; font-size:10px; }
        .info-box strong { display:block; color:#1e3a6f; font-size:12px; margin-top:4px; }
        .detail-list { margin-top:14px; border-top:1px solid #eef2f7; }
        .detail-row { display:flex; justify-content:space-between; gap:16px; padding:9px 0; border-bottom:1px solid #eef2f7; color:#64748b; font-size:11px; }
        .detail-row strong { color:#1e3a6f; text-align:right; }
        .result-actions { display:flex; gap:8px; margin-top:14px; }
        .result-actions a { flex:1; text-align:center; padding:10px; border-radius:8px; font-size:11px; font-weight:800; }
        .primary-action { background:#0f4aa5; color:#fff; }
        .secondary-action { border:1px solid #93c5fd; color:#2563eb; }
        .empty-result { padding:24px; background:#fff; border:1px solid #e2e8f0; border-radius:14px; text-align:center; color:#64748b; }
        .add-book-action { display:inline-flex; margin-top:12px; padding:10px 14px; border-radius:8px; background:#0f4aa5; color:#fff; font-size:11px; font-weight:800; }
        @media (max-width:600px) { .info-grid { grid-template-columns:1fr; } .search-head h1 { font-size:26px; } }
    </style>

    <div class="search-page">
        <a class="back-dashboard" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}">&larr; Kembali ke Dashboard</a>
        <div class="search-head"><div><h1>Cari Buku</h1><p class="search-subtitle">Cari atau scan barcode untuk menemukan buku.</p></div></div>

        <div class="mode-switch"><button type="button" class="mode-button active" id="manualMode">⌕ &nbsp; Cari Manual<br><small>Cari dengan kata kunci</small></button><button type="button" class="mode-button" id="scanMode">▣ &nbsp; Scan Barcode<br><small>Scan barcode buku</small></button></div>
        <form class="search-form" method="GET" action="{{ route('buku.cari') }}"><span style="color:#94a3b8;font-size:20px;">⌕</span><input id="searchInput" type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul, kode buku, atau ISBN..." autofocus><button class="search-submit" type="submit">Cari</button></form>

        <div class="scanner-panel" id="scannerPanel"><strong>Scan Barcode Buku</strong><p style="margin:5px 0;font-size:12px;color:#dbeafe;">Izinkan akses kamera, arahkan barcode ke kamera, lalu hasilnya akan dicari otomatis.</p><button type="button" id="startScanner" class="search-submit" style="background:#fff;color:#164194;">Aktifkan Kamera</button><video id="barcodeVideo" playsinline></video><div id="scannerMessage" style="font-size:11px;margin-top:8px;color:#bfdbfe;"></div></div>

        <div class="result-label"><span>Hasil Pencarian</span><span>{{ $bukus->total() }} buku</span></div>
        @forelse($bukus as $buku)
            <article class="result-card" style="margin-bottom:12px;">
                <div class="book-summary">
                    @if($buku->cover)<img class="book-cover" src="{{ asset('storage/' . $buku->cover) }}" alt="Cover {{ $buku->judul }}">@else<div class="book-cover">COVER<br>BUKU</div>@endif
                    <div><h2>{{ $buku->judul }}</h2><p>Kode Buku: <strong>{{ $buku->kode_buku }}</strong></p><p>ISBN: {{ $buku->isbn ?? '-' }}</p><p>EISBN: {{ $buku->eisbn ?? '-' }}</p><div class="tags"><span class="tag">{{ $buku->kategori ?? 'Umum' }}</span><span class="tag">{{ $buku->rak?->kode_rak ?? 'Belum ada rak' }}</span><span class="tag green">{{ $buku->stok }} Buku</span></div></div>
                </div>
                <div class="info-grid"><div class="info-box"><small>Lokasi Rak</small><strong>{{ $buku->rak?->kode_rak ?? '-' }}</strong></div><div class="info-box"><small>Stok Tersedia</small><strong>{{ $buku->stok }} Buku</strong></div><div class="info-box"><small>Status</small><strong style="color:{{ $buku->stok > 0 ? '#15803d' : '#dc2626' }}">{{ $buku->stok > 0 ? 'Tersedia' : 'Habis' }}</strong></div></div>
                <div class="detail-list"><div class="detail-row"><span>Judul Lengkap</span><strong>{{ $buku->judul }}</strong></div><div class="detail-row"><span>Kategori</span><strong>{{ $buku->kategori ?? 'Umum' }}</strong></div><div class="detail-row"><span>Jumlah Halaman</span><strong>{{ $buku->jumlah_halaman ?? '-' }} halaman</strong></div><div class="detail-row"><span>Lokasi</span><strong>{{ $buku->rak?->nama_lokasi ?? 'Belum ditempatkan' }}</strong></div></div>
                <div class="result-actions"><a class="secondary-action" href="{{ route('buku.show', ['buku' => $buku, 'from' => 'cari', 'q' => request('q')]) }}">Lihat Detail</a>@if(auth()->user()->isStaff())<a class="primary-action" href="{{ route('riwayat.index', ['from' => 'cari', 'q' => request('q')]) }}">Riwayat Penempatan</a>@endif</div>
            </article>
        @empty
            <div class="empty-result">
                Belum ada buku yang cocok. Coba gunakan kode, judul, ISBN, atau nomor rak lain.
                @if(request('q'))
                    <a class="add-book-action" href="{{ route('buku.create-data', ['isbn' => request('q')]) }}">Tambah Buku Baru dengan ISBN Ini</a>
                @endif
            </div>
        @endforelse
        <div style="margin-top:16px;">{{ $bukus->links() }}</div>
    </div>

    <script>
        const manualMode = document.getElementById('manualMode');
        const scanMode = document.getElementById('scanMode');
        const scannerPanel = document.getElementById('scannerPanel');
        const searchInput = document.getElementById('searchInput');
        const video = document.getElementById('barcodeVideo');
        const message = document.getElementById('scannerMessage');
        let stream;
        manualMode.addEventListener('click', () => { manualMode.classList.add('active'); scanMode.classList.remove('active'); scannerPanel.classList.remove('open'); });
        scanMode.addEventListener('click', () => { scanMode.classList.add('active'); manualMode.classList.remove('active'); scannerPanel.classList.add('open'); });
        document.getElementById('startScanner').addEventListener('click', async function () {
            if (!('BarcodeDetector' in window) || !navigator.mediaDevices?.getUserMedia) { message.textContent = 'Scanner otomatis tidak didukung browser ini. Masukkan ISBN atau barcode pada kolom pencarian.'; searchInput.focus(); return; }
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                video.srcObject = stream; video.style.display = 'block'; await video.play();
                const detector = new BarcodeDetector();
                message.textContent = 'Arahkan barcode ke kamera...';
                const scan = async () => { if (video.readyState >= 2) { const codes = await detector.detect(video); if (codes.length) { searchInput.value = codes[0].rawValue; stream.getTracks().forEach(track => track.stop()); document.querySelector('.search-form').submit(); return; } } requestAnimationFrame(scan); };
                scan();
            } catch (error) { message.textContent = 'Kamera tidak dapat diakses. Periksa izin kamera perangkat.'; }
        });
    </script>
@endsection
