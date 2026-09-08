@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .intake-page {
        max-width: 880px;
        margin: 0 auto;
        padding: 24px 16px 48px;
        font-family: 'Inter', sans-serif;
    }

    /* Back Button */
    .back-button {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 18px; border-radius: 12px; background: #fff;
        border: 1px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02); margin-bottom: 24px;
    }
    .back-button:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; transform: translateX(-2px); }

    /* Header */
    .intake-heading { margin-bottom: 28px; }
    .eyebrow { display: inline-block; padding: 4px 12px; background: #e0e7ff; color: #4338ca; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 10px; }
    h1 { margin: 0; color: #0f172a; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
    .subtitle { margin: 6px 0 0; color: #64748b; font-size: 14px; }

    /* Steps Tracker */
    .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 28px; }
    .step { padding: 14px 18px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; color: #64748b; font-size: 12px; font-weight: 500; box-shadow: 0 2px 6px rgba(0,0,0,0.02); transition: all 0.2s; }
    .step strong { display: block; margin-bottom: 4px; color: #1e293b; font-size: 14px; font-weight: 700; }
    .step.active { border-color: #93c5fd; background: #f0f7ff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.06); }
    .step.active strong { color: #1d4ed8; }

    /* Cards */
    .card { padding: 28px; background: #fff; border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); margin-bottom: 24px; }
    .card h2 { margin: 0 0 18px; color: #0f172a; font-size: 18px; font-weight: 700; letter-spacing: -0.3px; display: flex; align-items: center; gap: 8px; }

    .scan-card { border: 1px solid #bfdbfe; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); box-shadow: 0 12px 35px rgba(22, 81, 164, 0.08); position: relative; overflow: hidden; }
    .scan-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #1C396A, #1651A4, #357A38, #F7D60A); }
    .scan-badge { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; padding: 4px 12px; border-radius: 20px; background: #dbeafe; color: #1e40af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Form Fields */
    .field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .field { margin-bottom: 18px; }
    label { display: block; margin-bottom: 8px; color: #334155; font-size: 13px; font-weight: 600; }
    input[type=text], input[type=number], input[type=file], select {
        width: 100%; padding: 12px 16px; border: 1.5px solid #cbd5e1; border-radius: 12px; color: #0f172a; font-size: 14px; background: #fff;
        transition: all 0.2s ease; box-sizing: border-box;
    }
    input[type=text]:focus, input[type=number]:focus, select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12); }

    .scan-row { display: flex; gap: 12px; align-items: center; }
    .scan-row input { flex: 1; font-weight: 600; letter-spacing: 0.5px; }
    .scan-help { margin: 8px 0 0; color: #64748b; font-size: 12px; display: flex; align-items: center; gap: 6px; }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; border: 0; border-radius: 12px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s ease; }
    .btn-blue { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .btn-blue:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3); }
    .btn-dark { background: #0f172a; color: #fff; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15); }
    .btn-dark:hover { background: #1e293b; transform: translateY(-1px); }
    .btn-light { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-light:hover { background: #e2e8f0; color: #0f172a; }
    .btn-red { background: #ef4444; color: #fff; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); }
    .btn-red:hover { background: #dc2626; }

    /* Camera Container */
    .camera { display: none; margin-top: 18px; position: relative; border-radius: 16px; overflow: hidden; background: #090d16; border: 2px solid #3b82f6; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); }
    .camera video { display: block; width: 100%; max-height: 320px; object-fit: cover; }
    .camera-actions { position: absolute; bottom: 12px; right: 12px; }

    /* Known Item Box */
    .known-item { display: none; align-items: center; gap: 16px; margin: 16px 0 20px; padding: 16px; border: 1px solid #bbf7d0; border-radius: 14px; background: #f0fdf4; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.05); }
    .known-item img { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; background: #dcfce7; border: 1px solid #a7f3d0; }
    .known-item strong { display: block; color: #14532d; font-size: 15px; font-weight: 700; margin-bottom: 2px; }
    .known-item span { color: #166534; font-size: 12px; font-weight: 500; }

    /* Alerts */
    .alert { margin-bottom: 24px; padding: 14px 18px; border-radius: 14px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
    .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    .actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px; }
    .manual-note { margin-bottom: 20px; padding: 14px 16px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; color: #92400e; font-size: 13px; line-height: 1.5; }
    .manual-client { display: none; }

    @media (max-width: 650px) {
        .intake-heading { display: block; }
        .steps, .field-grid { grid-template-columns: 1fr; }
        .scan-row { flex-direction: column; align-items: stretch; }
        .btn { width: 100%; }
        .actions { flex-direction: column-reverse; }
    }
</style>

<div class="intake-page">
    <a class="back-button" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff') }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali
    </a>

    <div class="intake-heading">
        <div>
            <span class="eyebrow">Penerimaan Inventaris</span>
            <h1>Scan & Input Barang</h1>
            <p class="subtitle">Pilih kategori dan rak, lalu scan barcode untuk memperbarui stok gudang.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Steps Tracker -->
    <div class="steps">
        <div class="step active"><strong>1. Tentukan Lokasi</strong>Kategori dan rak tujuan</div>
        <div class="step active"><strong>2. Scan Barcode</strong>Gunakan kamera / ketik kode</div>
        <div class="step"><strong>3. Lengkapi Data</strong>Khusus barang yang baru</div>
    </div>

    <!-- Scanner Section -->
    <div class="card scan-card">
        <span class="scan-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z" /></svg>
            Fitur Utama
        </span>
        <h2>Scan Barcode Barang</h2>
        <p class="subtitle" style="margin:-8px 0 20px;">Arahkan barcode ke kamera atau ketik kode. Data barang yang sudah dikenal akan terisi otomatis.</p>

        <form method="POST" action="{{ route('pencarian.input.proses') }}">
            @csrf
            <div class="field">
                <label for="kode_barang">Barcode / Kode Barang *</label>
                <div class="scan-row">
                    <input id="kode_barang" type="text" name="kode_barang" value="{{ old('kode_barang', $manualData['kode_barang'] ?? '') }}" placeholder="Scan barcode atau ketik kode barang" autocomplete="off" required>
                    <button class="btn btn-dark" type="button" id="camera-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px; height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                        Buka Kamera
                    </button>
                </div>
                <p class="scan-help" id="scan-status">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                    Scanner siap. Setelah kode terbaca, data akan dicari otomatis.
                </p>
                <div class="camera" id="camera-box">
                    <video id="camera-video" playsinline></video>
                    <div class="camera-actions">
                        <button class="btn btn-red" type="button" id="camera-stop">Tutup Kamera</button>
                    </div>
                </div>
            </div>

            <div class="known-item" id="known-item">
                <img id="known-image" alt="Foto barang">
                <div>
                    <strong id="known-name"></strong>
                    <span id="known-meta"></span>
                </div>
            </div>

            <h2 style="margin-top:28px;">Data Penerimaan</h2>
            <div class="field-grid">
                <div class="field">
                    <label for="kategori_id">Kategori Barang *</label>
                    <select id="kategori_id" name="kategori_id" required>
                        <option value="">-- Pilih kategori --</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $manualData['kategori_id'] ?? '') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }} ({{ $kategori->kode_kategori }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="rak_id">Lokasi Rak Tujuan *</label>
                    <select id="rak_id" name="rak_id" required>
                        <option value="">-- Pilih rak --</option>
                        @foreach($raks as $rak)
                            <option value="{{ $rak->id }}" {{ old('rak_id', $manualData['rak_id'] ?? '') == $rak->id ? 'selected' : '' }}>
                                {{ $rak->kode_rak }} - {{ $rak->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="jumlah">Jumlah Masuk *</label>
                <input id="jumlah" type="number" name="jumlah" value="{{ old('jumlah', $manualData['jumlah'] ?? 1) }}" min="1" required>
            </div>

            <div class="actions">
                <a class="btn btn-light" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff') }}">Batal</a>
                <button class="btn btn-blue" type="submit">Proses Barcode</button>
            </div>
        </form>
    </div>

    <!-- Form Manual Server Side -->
    @isset($manualData)
    <div class="card">
        <h2>Barang Belum Dikenali</h2>
        <p class="manual-note">Kode <strong>{{ $manualData['kode_barang'] }}</strong> belum ada di database. Lengkapi data berikut agar barang dibuat dan stok awalnya dicatat di rak yang dipilih.</p>
        <form method="POST" action="{{ route('pencarian.input.manual') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="kategori_id" value="{{ $manualData['kategori_id'] }}">
            <input type="hidden" name="rak_id" value="{{ $manualData['rak_id'] }}">
            <input type="hidden" name="kode_barang" value="{{ $manualData['kode_barang'] }}">
            <input type="hidden" name="jumlah" value="{{ $manualData['jumlah'] }}">

            <div class="field">
                <label for="nama">Nama Barang *</label>
                <input id="nama" type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Kursi Kantor" required>
            </div>
            <div class="field">
                <label for="gambar">Foto Barang</label>
                <input id="gambar" type="file" name="gambar" accept="image/*">
                <p class="scan-help">Opsional, maksimal 2MB.</p>
            </div>
            <div class="actions">
                <button class="btn btn-blue" type="submit">Simpan Barang Baru</button>
            </div>
        </form>
    </div>
    @endisset

    <!-- Form Manual Client Side (AJAX) -->
    <div class="card manual-client" id="manual-client">
        <h2>Barcode Belum Dikenali</h2>
        <p class="manual-note">Kode ini belum ada di database. Lengkapi nama dan foto barang untuk membuat data baru.</p>
        <form method="POST" action="{{ route('pencarian.input.manual') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="kategori_id" id="manual-kategori">
            <input type="hidden" name="rak_id" id="manual-rak">
            <input type="hidden" name="kode_barang" id="manual-kode">
            <input type="hidden" name="jumlah" id="manual-jumlah">

            <div class="field">
                <label for="manual-nama">Nama Barang *</label>
                <input id="manual-nama" type="text" name="nama" placeholder="Contoh: Kursi Kantor" required>
            </div>
            <div class="field">
                <label for="manual-gambar">Foto Barang</label>
                <input id="manual-gambar" type="file" name="gambar" accept="image/*">
            </div>
            <div class="actions">
                <button class="btn btn-blue" type="submit">Simpan Barang Baru</button>
            </div>
        </form>
    </div>
</div>

<script>
const cameraButton = document.getElementById('camera-button');
const cameraStop = document.getElementById('camera-stop');
const cameraBox = document.getElementById('camera-box');
const cameraVideo = document.getElementById('camera-video');
const codeInput = document.getElementById('kode_barang');
const scanStatus = document.getElementById('scan-status');
const categorySelect = document.getElementById('kategori_id');
const rackSelect = document.getElementById('rak_id');
const quantityInput = document.getElementById('jumlah');
const knownItem = document.getElementById('known-item');
const knownImage = document.getElementById('known-image');
const knownName = document.getElementById('known-name');
const knownMeta = document.getElementById('known-meta');
const manualClient = document.getElementById('manual-client');
let lookupTimer;
let cameraStream;
let detector;
let scanning = false;

async function lookupCode() {
    const code = codeInput.value.trim();
    if (!code) {
        knownItem.style.display = 'none';
        manualClient.style.display = 'none';
        scanStatus.textContent = 'Scanner siap. Setelah kode terbaca, data akan dicari otomatis.';
        return;
    }
    scanStatus.textContent = 'Mencari data barang...';
    try {
        const response = await fetch(`{{ route('pencarian.input.lookup') }}?kode_barang=${encodeURIComponent(code)}`, { headers: { Accept: 'application/json' } });
        const result = await response.json();
        if (result.found) {
            categorySelect.value = result.barang.kategori_id;
            knownName.textContent = result.barang.nama;
            knownMeta.textContent = `${result.barang.kategori || 'Tanpa kategori'} · Stok saat ini ${result.barang.stok} ${result.barang.satuan}`;
            knownImage.src = result.barang.gambar || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="48" height="48"%3E%3Crect width="100%25" height="100%25" fill="%23dcfce7"/%3E%3C/svg%3E';
            knownItem.style.display = 'flex';
            manualClient.style.display = 'none';
            scanStatus.textContent = 'Barang dikenali. Data kategori terisi otomatis, tentukan rak dan jumlah masuk.';
        } else {
            knownItem.style.display = 'none';
            manualClient.style.display = 'block';
            document.getElementById('manual-kategori').value = categorySelect.value;
            document.getElementById('manual-rak').value = rackSelect.value;
            document.getElementById('manual-kode').value = code;
            document.getElementById('manual-jumlah').value = quantityInput.value;
            scanStatus.textContent = 'Barcode belum dikenali. Lengkapi data barang baru di bawah.';
        }
    } catch (error) {
        scanStatus.textContent = 'Data belum dapat dicari. Periksa koneksi lalu coba lagi.';
    }
}

codeInput.addEventListener('input', function() {
    clearTimeout(lookupTimer);
    lookupTimer = setTimeout(lookupCode, 500);
});
codeInput.addEventListener('change', lookupCode);
categorySelect.addEventListener('change', syncManualData);
rackSelect.addEventListener('change', syncManualData);
quantityInput.addEventListener('input', syncManualData);

function syncManualData() {
    document.getElementById('manual-kategori').value = categorySelect.value;
    document.getElementById('manual-rak').value = rackSelect.value;
    document.getElementById('manual-kode').value = codeInput.value.trim();
    document.getElementById('manual-jumlah').value = quantityInput.value;
}

async function scanFrame() {
    if (!scanning || !detector) return;
    try {
        const results = await detector.detect(cameraVideo);
        if (results.length) {
            codeInput.value = results[0].rawValue;
            scanStatus.textContent = 'Barcode terbaca. Mencari data barang...';
            stopCamera();
            lookupCode();
            return;
        }
    } catch (error) {
        scanStatus.textContent = 'Scanner tidak dapat membaca kamera ini.';
    }
    requestAnimationFrame(scanFrame);
}

async function startCamera() {
    if (!('BarcodeDetector' in window) || !navigator.mediaDevices?.getUserMedia) {
        scanStatus.textContent = 'Scanner otomatis tidak didukung browser ini. Masukkan barcode secara manual.';
        codeInput.focus();
        return;
    }
    try {
        detector = new BarcodeDetector();
        cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } } });
        cameraVideo.srcObject = cameraStream;
        await cameraVideo.play();
        cameraBox.style.display = 'block';
        cameraButton.style.display = 'none';
        scanning = true;
        scanStatus.textContent = 'Arahkan barcode ke kamera.';
        scanFrame();
    } catch (error) {
        scanStatus.textContent = 'Kamera tidak dapat dibuka. Masukkan barcode secara manual.';
    }
}

function stopCamera() {
    scanning = false;
    cameraStream?.getTracks().forEach(track => track.stop());
    cameraStream = null;
    cameraVideo.srcObject = null;
    cameraBox.style.display = 'none';
    cameraButton.style.display = 'inline-flex';
}

cameraButton.addEventListener('click', startCamera);
cameraStop.addEventListener('click', stopCamera);
window.addEventListener('beforeunload', stopCamera);
</script>
@endsection
