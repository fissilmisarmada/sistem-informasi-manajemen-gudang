@extends('layouts.app')

@section('content')
    <style>
        .stock-page { max-width:900px; margin:0 auto; padding:12px 0 34px; }
        .back-dashboard { display:inline-flex; align-items:center; gap:7px; margin-bottom:24px; padding:13px 16px; border-radius:12px; background:#e2e8f0; color:#0f2748; font-size:16px; font-weight:800; line-height:1; text-decoration:none; }
        .back-dashboard:hover, .back-dashboard:focus-visible { background:#cbd5e1; outline:0; }
        .stock-head { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
        .stock-head h1 { margin:0; color:#123b82; font-size:30px; }
        .stock-head p { margin:4px 0 0; color:#64748b; font-size:12px; }
        .stock-form { background:#fff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 7px 20px rgba(15,23,42,.05); overflow:hidden; }
        .form-section { padding:20px; border-bottom:1px solid #eef2f7; }
        .form-section:last-child { border-bottom:0; }
        .form-section h2 { margin:0 0 16px; color:#1e3a6f; font-size:15px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:14px 18px; }
        .form-field label { display:block; margin-bottom:6px; color:#334e70; font-size:11px; font-weight:800; }
        .form-field label span { color:#dc2626; }
        .form-field input, .form-field select { width:100%; height:40px; padding:0 11px; border:1px solid #d5dee9; border-radius:8px; color:#526b87; background:#fff; font-size:12px; box-sizing:border-box; }
        .form-field input:focus, .form-field select:focus { outline:0; border-color:#2563a6; box-shadow:0 0 0 3px #dbeafe; }
        .barcode-line { display:flex; gap:8px; }
        .barcode-line input { flex:1; }
        .barcode-button { border:1px solid #9fc3e5; border-radius:8px; background:#fff; color:#07539a; padding:0 14px; font-size:11px; font-weight:800; cursor:pointer; }
        .barcode-scanner { display:none; margin-top:9px; padding:10px; background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; color:#315b91; font-size:11px; }
        .barcode-scanner.visible { display:block; }
        #barcodeVideo { display:block; width:100%; max-height:220px; margin-top:8px; border-radius:7px; background:#0b2554; }
        .cover-upload { display:flex; align-items:center; justify-content:center; min-height:82px; border:1px dashed #b7c9dc; border-radius:9px; color:#2563eb; cursor:pointer; text-align:center; font-size:11px; }
        .cover-upload input { display:none; }
        .cover-upload small { display:block; color:#94a3b8; margin-top:5px; }
        .form-actions { display:flex; justify-content:flex-end; gap:10px; padding:17px 20px; background:#f8fafc; }
        .form-actions a, .form-actions button { min-width:135px; height:40px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; font-size:12px; font-weight:800; cursor:pointer; text-decoration:none; box-sizing:border-box; }
        .cancel-button { border:1px solid #93bdf0; background:#fff; color:#2563eb; }
        .save-button { border:0; background:#07539a; color:#fff; }
        .form-errors { margin-bottom:16px; padding:11px 14px; border:1px solid #f5b8b8; border-radius:9px; background:#fff0f0; color:#a12626; font-size:12px; }
        .form-errors ul { margin:0; padding-left:18px; }
        @media (max-width:650px) { .form-grid { grid-template-columns:1fr; } .stock-head h1 { font-size:25px; } .form-actions { position:sticky; bottom:0; } }
    </style>

    <div class="stock-page">
        <a class="back-dashboard" href="{{ route('dashboard.staff') }}">&larr; Kembali</a>
        <div class="stock-head"><div><h1>Input Stok Buku Baru</h1><p>Tambah buku baru dan masukkan jumlah stok yang diterima.</p></div></div>

        @if($errors->any())
            <div class="form-errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form class="stock-form" method="POST" action="{{ route('buku.store-data') }}" enctype="multipart/form-data">
            @csrf
            <section class="form-section">
                <h2>Informasi Buku</h2>
                <div class="form-grid">
                    <div class="form-field" style="grid-column:span 2;"><label for="barcode">Scan Barcode</label><div class="barcode-line"><input id="barcode" type="text" value="{{ request('isbn', old('isbn')) }}" placeholder="Scan barcode atau masukkan ISBN secara manual"><button class="barcode-button" type="button" id="scanBarcode">▣ &nbsp; Aktifkan Kamera</button></div><div id="barcodeScanner" class="barcode-scanner" data-auto-scan="{{ request()->boolean('auto_scan') ? '1' : '0' }}"><span id="scannerMessage">Arahkan barcode buku ke kamera.</span><video id="barcodeVideo" playsinline></video></div><small style="display:block;margin-top:6px;color:#94a3b8;font-size:10px;">Barcode akan mengisi ISBN secara otomatis, lalu lengkapi data buku baru.</small></div>
                    <div class="form-field"><label for="kode_buku">Kode Buku <span>*</span></label><input id="kode_buku" name="kode_buku" value="{{ old('kode_buku') }}" placeholder="Contoh: SI001" required></div>
                    <div class="form-field"><label for="judul">Judul Buku <span>*</span></label><input id="judul" name="judul" value="{{ old('judul') }}" placeholder="Masukkan judul buku" required></div>
                    <div class="form-field"><label for="isbn">ISBN <span>*</span></label><input id="isbn" name="isbn" value="{{ old('isbn') }}" placeholder="Contoh: 978-623-1234-07-4" required></div>
                    <div class="form-field"><label for="eisbn">E-ISBN</label><input id="eisbn" name="eisbn" value="{{ old('eisbn') }}" placeholder="Masukkan E-ISBN (jika ada)"></div>
                    <div class="form-field"><label for="kategori">Kategori <span>*</span></label><input id="kategori" name="kategori" value="{{ old('kategori') }}" placeholder="Contoh: Teknologi Informasi"></div>
                    <div class="form-field"><label for="jumlah_halaman">Jumlah Halaman</label><input id="jumlah_halaman" name="jumlah_halaman" type="number" min="1" value="{{ old('jumlah_halaman') }}" placeholder="Contoh: 320"></div>
                    <div class="form-field"><label for="cover">Cover Buku</label><label class="cover-upload" for="cover"><span>☁ &nbsp; Upload Cover Buku<small>Format JPG/PNG, maksimal 2MB</small></span><input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp"></label></div>
                </div>
            </section>
            <section class="form-section">
                <h2>Informasi Stok</h2>
                <div class="form-grid">
                    <div class="form-field"><label for="stok">Jumlah Stok <span>*</span></label><input id="stok" name="stok" type="number" min="0" value="{{ old('stok', 0) }}" placeholder="Masukkan jumlah stok" required></div>
                    <div class="form-field"><label for="rak_id">Rak Penyimpanan</label><select id="rak_id" name="rak_id"><option value="">Pilih rak penyimpanan</option>@foreach($dataRak as $rak)<option value="{{ $rak->id }}" @selected(old('rak_id') == $rak->id)>{{ $rak->kode_rak }} - {{ $rak->nama_lokasi }}</option>@endforeach</select></div>
                </div>
            </section>
            <div class="form-actions"><a class="cancel-button" href="{{ route('dashboard.staff') }}">&times; &nbsp; Batal</a><button class="save-button" type="submit">▣ &nbsp; Simpan Data Buku</button></div>
        </form>
    </div>

    <script>
        const barcodeInput = document.getElementById('barcode');
        const isbnInput = document.getElementById('isbn');
        const scannerPanel = document.getElementById('barcodeScanner');
        const scannerMessage = document.getElementById('scannerMessage');
        const barcodeVideo = document.getElementById('barcodeVideo');
        let barcodeStream;
        function copyBarcodeToIsbn() { isbnInput.value = barcodeInput.value.trim(); }
        async function startBarcodeScanner() {
            scannerPanel.classList.add('visible');
            if (!('BarcodeDetector' in window) || !navigator.mediaDevices?.getUserMedia) {
                scannerMessage.textContent = 'Scanner otomatis tidak didukung browser ini. Masukkan barcode secara manual.';
                barcodeInput.focus();
                return;
            }
            try {
                barcodeStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                barcodeVideo.srcObject = barcodeStream;
                await barcodeVideo.play();
                const detector = new BarcodeDetector();
                scannerMessage.textContent = 'Arahkan barcode buku ke kamera...';
                const scan = async () => {
                    if (!barcodeStream) return;
                    if (barcodeVideo.readyState >= 2) {
                        const codes = await detector.detect(barcodeVideo);
                        if (codes.length) {
                            barcodeInput.value = codes[0].rawValue;
                            copyBarcodeToIsbn();
                            barcodeStream.getTracks().forEach(track => track.stop());
                            barcodeStream = null;
                            scannerMessage.textContent = 'Barcode berhasil dibaca. Lengkapi data buku baru.';
                            return;
                        }
                    }
                    requestAnimationFrame(scan);
                };
                scan();
            } catch (error) { scannerMessage.textContent = 'Kamera tidak dapat diakses. Periksa izin kamera perangkat.'; }
        }
        document.getElementById('scanBarcode').addEventListener('click', startBarcodeScanner);
        barcodeInput.addEventListener('change', copyBarcodeToIsbn);
        const autoScan = scannerPanel.dataset.autoScan === '1';
        if (autoScan) startBarcodeScanner();
        document.getElementById('barcode').addEventListener('change', function () {
            document.getElementById('isbn').value = this.value.trim();
        });
    </script>
@endsection
