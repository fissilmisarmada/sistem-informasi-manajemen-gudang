@extends('layouts.app')

@section('content')
    <style>
        .place-page { max-width:900px; margin:0 auto; padding:12px 0 34px; }
        .place-head { display:flex; align-items:center; gap:12px; margin-bottom:16px; }
        .place-head h1 { margin:0; color:#123b82; font-size:30px; }
        .place-head p { margin:4px 0 0; color:#64748b; font-size:12px; }
        .place-back { color:#2563eb; font-size:25px; text-decoration:none; }
        .notice { display:flex; gap:9px; padding:11px 13px; background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; color:#315b91; font-size:11px; margin-bottom:13px; }
        .place-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 6px 18px rgba(15,23,42,.05); padding:16px; margin-bottom:12px; }
        .step-title { display:flex; align-items:center; gap:8px; color:#1e3a6f; font-weight:900; font-size:13px; margin-bottom:11px; }
        .step-number { width:21px; height:21px; display:grid; place-items:center; border-radius:50%; background:#07539a; color:#fff; font-size:11px; }
        .search-line { display:flex; gap:8px; }
        .search-line input, .place-input, .place-select { width:100%; height:39px; border:1px solid #d5dee9; border-radius:8px; padding:0 11px; color:#526b87; background:#fff; font-size:12px; box-sizing:border-box; }
        .search-line input:focus, .place-input:focus, .place-select:focus { outline:0; border-color:#2563a6; box-shadow:0 0 0 3px #dbeafe; }
        .scan-button { white-space:nowrap; border:1px solid #93bdf0; background:#fff; color:#07539a; border-radius:8px; padding:0 13px; font-size:11px; font-weight:800; cursor:pointer; }
        .book-selected { display:none; grid-template-columns:62px 1fr auto; gap:12px; align-items:center; padding:12px; margin-top:10px; background:#f8fbff; border:1px solid #dbeafe; border-radius:9px; }
        .book-selected.visible { display:grid; }
        .book-cover { width:62px; height:80px; object-fit:cover; border-radius:6px; background:#dbeafe; display:grid; place-items:center; color:#2563eb; font-size:10px; font-weight:800; text-align:center; }
        .book-selected h3 { margin:0 0 5px; color:#1e3a6f; font-size:13px; }
        .book-selected p { margin:2px 0; color:#64748b; font-size:10px; }
        .stock-current { color:#15803d; font-size:20px; font-weight:900; text-align:center; }
        .stock-current small { display:block; color:#64748b; font-size:9px; font-weight:600; }
        .place-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .field-label { display:block; margin-bottom:6px; color:#334e70; font-size:11px; font-weight:800; }
        .capacity-info { display:none; margin-top:9px; padding:11px; background:#f8fbff; border:1px solid #dbeafe; border-radius:8px; color:#526b87; font-size:11px; }
        .capacity-info.visible { display:block; }
        .capacity-info strong { color:#1e3a6f; }
        .capacity-bar { height:6px; margin-top:8px; border-radius:6px; background:#e2e8f0; overflow:hidden; }
        .capacity-bar span { display:block; height:100%; width:0; background:#16a34a; transition:width .2s; }
        .summary { display:grid; grid-template-columns:1fr 1fr; gap:6px 20px; font-size:11px; color:#64748b; }
        .summary div { display:flex; justify-content:space-between; gap:10px; border-bottom:1px dashed #e2e8f0; padding:5px 0; }
        .summary strong { color:#1e3a6f; text-align:right; }
        .place-actions { display:flex; gap:9px; margin-top:12px; }
        .place-actions button, .place-actions a { flex:1; height:39px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; font-size:11px; font-weight:800; text-decoration:none; cursor:pointer; box-sizing:border-box; }
        .confirm-button { border:0; background:#07539a; color:#fff; }
        .cancel-button { border:1px solid #93bdf0; color:#2563eb; background:#fff; }
        .history-row { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid #eef2f7; font-size:10px; }
        .history-row:last-child { border-bottom:0; }
        .history-icon { width:28px; height:32px; display:grid; place-items:center; border-radius:6px; background:#dcfce7; color:#15803d; font-weight:900; }
        .history-copy { flex:1; color:#64748b; }
        .history-copy strong { display:block; color:#1e3a6f; font-size:11px; }
        .success { padding:11px 13px; background:#dcfce7; border:1px solid #86efac; color:#166534; border-radius:8px; margin-bottom:12px; font-size:12px; font-weight:700; }
        .errors { padding:11px 13px; background:#fff0f0; border:1px solid #f5b8b8; color:#a12626; border-radius:8px; margin-bottom:12px; font-size:12px; }
        .errors ul { margin:0; padding-left:18px; }
        @media(max-width:650px) { .place-grid, .summary { grid-template-columns:1fr; } .book-selected { grid-template-columns:54px 1fr; } .stock-current { grid-column:span 2; text-align:left; } }
    </style>

    <div class="place-page">
        <a class="back-dashboard" href="{{ route('dashboard.staff') }}">&larr; Kembali ke Dashboard</a>
        <div class="place-head"><a class="place-back" href="{{ route('dashboard.staff') }}">&larr;</a><div><h1>Penempatan Buku Masuk</h1><p>Atur lokasi buku ke rak tujuan.</p></div></div>

        @if(session('sukses'))<div class="success">{{ session('sukses') }}</div>@endif
        @if($errors->any())<div class="errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="notice"><strong>i</strong><span>Buku yang sudah tersedia di data akan diproses. Masukkan jumlah buku masuk dan pilih rak tujuan.</span></div>

        <form id="placementForm" method="POST" action="{{ route('buku.store') }}">
            @csrf
            <section class="place-card">
                <div class="step-title"><span class="step-number">1</span>Pilih Buku</div>
                <div class="search-line"><input id="bookSearch" type="search" placeholder="Cari buku berdasarkan kode atau judul..."><button type="button" id="scanButton" class="scan-button">▣ &nbsp; Scan Barcode</button></div>
                <select id="buku_id" name="buku_id" class="place-select" required style="margin-top:8px;"><option value="">Pilih buku</option>@foreach($dataBuku as $buku)<option value="{{ $buku->id }}" data-title="{{ $buku->judul }}" data-code="{{ $buku->kode_buku }}" data-isbn="{{ $buku->isbn }}" data-stock="{{ $buku->stok }}" data-cover="{{ $buku->cover ? asset('storage/' . $buku->cover) : '' }}" data-rak="{{ $buku->rak?->kode_rak ?? '' }}">{{ $buku->judul }} ({{ $buku->kode_buku }})</option>@endforeach</select>
                <div id="bookSelected" class="book-selected"><div id="bookCover" class="book-cover">BUKU</div><div><h3 id="bookTitle">-</h3><p id="bookCode">Kode: -</p><p id="bookIsbn">ISBN: -</p></div><div class="stock-current"><span id="bookStock">0</span><small>Stok Saat Ini</small></div></div>
            </section>

            <section class="place-card">
                <div class="step-title"><span class="step-number">2</span>Jumlah Buku Masuk</div>
                <div style="display:flex;align-items:center;gap:9px;max-width:320px;"><input class="place-input" id="jumlah_masuk" name="jumlah_masuk" type="number" min="1" value="{{ old('jumlah_masuk', 1) }}" required><span style="font-size:11px;color:#64748b;">buku</span></div>
                <small style="display:block;margin-top:7px;color:#94a3b8;font-size:10px;">Masukkan jumlah buku yang baru diterima.</small>
            </section>

            <section class="place-card">
                <div class="step-title"><span class="step-number">3</span>Pilih Rak Tujuan</div>
                <label class="field-label" for="rak_id">Rak Penyimpanan</label><select id="rak_id" name="rak_id" class="place-select" required><option value="">Pilih rak penyimpanan</option>@foreach($dataRak as $rak)<option value="{{ $rak->id }}" data-capacity="{{ $rak->kapasitas ?? 0 }}" data-used="{{ $rak->buku->sum('stok') }}">{{ $rak->kode_rak }} - {{ $rak->nama_lokasi }}</option>@endforeach</select>
                <div id="capacityInfo" class="capacity-info"><strong id="capacityName">Rak</strong><div style="display:flex;justify-content:space-between;margin-top:6px;"><span>Kapasitas: <b id="capacityTotal">-</b> buku</span><span>Terisi: <b id="capacityUsed">-</b> buku</span><span>Sisa: <b id="capacityRemaining">-</b> buku</span></div><div class="capacity-bar"><span id="capacityProgress"></span></div></div>
            </section>

            <section class="place-card">
                <div class="step-title"><span class="step-number">4</span>Konfirmasi Penempatan</div>
                <div class="summary"><div><span>Buku</span><strong id="summaryBook">-</strong></div><div><span>Jumlah Buku Masuk</span><strong id="summaryQuantity">-</strong></div><div><span>Rak Tujuan</span><strong id="summaryRack">-</strong></div><div><span>Stok Setelah Penempatan</span><strong id="summaryStock">-</strong></div></div>
                <div class="notice" style="margin:12px 0 0;">Setelah konfirmasi, stok akan bertambah dan lokasi buku akan tersimpan.</div>
                <div class="place-actions"><button class="confirm-button" type="submit">&#10003; &nbsp; Konfirmasi Penempatan</button><a class="cancel-button" href="{{ route('dashboard.staff') }}">&#10005; &nbsp; Batal</a></div>
            </section>
        </form>

        <section class="place-card"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;"><div class="step-title" style="margin:0;">Riwayat Penempatan Terakhir</div><a href="{{ route('riwayat.index') }}" style="font-size:10px;color:#2563eb;font-weight:800;">Lihat Semua</a></div><div class="history-row"><div class="history-icon">▦</div><div class="history-copy"><strong>Siap melakukan penempatan</strong><span>Pilih buku dan rak tujuan untuk memulai.</span></div></div></section>
    </div>

    <script>
        const bookSelect = document.getElementById('buku_id');
        const rackSelect = document.getElementById('rak_id');
        const quantityInput = document.getElementById('jumlah_masuk');
        const searchInput = document.getElementById('bookSearch');
        const selectedCard = document.getElementById('bookSelected');
        function updateBook() {
            const option = bookSelect.options[bookSelect.selectedIndex];
            if (!option || !option.value) { selectedCard.classList.remove('visible'); return; }
            selectedCard.classList.add('visible');
            document.getElementById('bookTitle').textContent = option.dataset.title;
            document.getElementById('bookCode').textContent = 'Kode: ' + option.dataset.code;
            document.getElementById('bookIsbn').textContent = 'ISBN: ' + (option.dataset.isbn || '-');
            document.getElementById('bookStock').textContent = option.dataset.stock || 0;
            const cover = document.getElementById('bookCover');
            cover.innerHTML = option.dataset.cover ? `<img src="${option.dataset.cover}" alt="Cover" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">` : 'BUKU';
            updateSummary();
        }
        function updateRack() {
            const option = rackSelect.options[rackSelect.selectedIndex];
            const box = document.getElementById('capacityInfo');
            if (!option || !option.value) { box.classList.remove('visible'); updateSummary(); return; }
            const total = Number(option.dataset.capacity || 0); const used = Number(option.dataset.used || 0); const remaining = total ? Math.max(0, total - used) : 'Tidak dibatasi';
            box.classList.add('visible'); document.getElementById('capacityName').textContent = option.textContent; document.getElementById('capacityTotal').textContent = total || 'Tidak dibatasi'; document.getElementById('capacityUsed').textContent = used; document.getElementById('capacityRemaining').textContent = remaining;
            document.getElementById('capacityProgress').style.width = total ? Math.min(100, used / total * 100) + '%' : '0%'; updateSummary();
        }
        function updateSummary() { const book = bookSelect.options[bookSelect.selectedIndex]; const rack = rackSelect.options[rackSelect.selectedIndex]; const stock = Number(book?.dataset.stock || 0); document.getElementById('summaryBook').textContent = book?.value ? book.dataset.title : '-'; document.getElementById('summaryQuantity').textContent = quantityInput.value ? quantityInput.value + ' buku' : '-'; document.getElementById('summaryRack').textContent = rack?.value ? rack.textContent : '-'; document.getElementById('summaryStock').textContent = book?.value ? stock + Number(quantityInput.value || 0) + ' buku' : '-'; }
        bookSelect.addEventListener('change', updateBook); rackSelect.addEventListener('change', updateRack); quantityInput.addEventListener('input', updateSummary);
        searchInput.addEventListener('input', function () { const term = this.value.toLowerCase(); Array.from(bookSelect.options).forEach((option, index) => { if (!index) return; option.hidden = term && !option.textContent.toLowerCase().includes(term); }); });
    document.getElementById('scanButton').addEventListener('click', function () { const value = prompt('Masukkan ISBN atau barcode buku:'); if (!value) return; const match = Array.from(bookSelect.options).find(option => option.dataset.isbn === value || option.dataset.code === value); if (match) { bookSelect.value = match.value; updateBook(); } else { alert('Buku tidak ditemukan. Silakan periksa ISBN atau kode buku.'); } });
    updateSummary();
    </script>
@endsection
