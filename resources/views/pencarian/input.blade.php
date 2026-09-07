@extends('layouts.app')

@section('content')
<style>
    .intake-page { max-width:900px; margin:0 auto; padding:10px 0 36px; }
    .intake-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:22px; }
    .eyebrow { color:#64748b; font-size:12px; font-weight:800; letter-spacing:1.1px; text-transform:uppercase; }
    h1 { margin:7px 0 0; color:#0f172a; font-size:32px; }
    .subtitle { margin:6px 0 0; color:#64748b; font-size:13px; }
    .btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:10px 16px; border:0; border-radius:9px; font-size:13px; font-weight:800; text-decoration:none; cursor:pointer; }
    .btn-blue { background:#2563eb; color:#fff; }
    .btn-dark { background:#0f172a; color:#fff; }
    .btn-light { background:#e2e8f0; color:#0f172a; }
    .btn-red { background:#dc2626; color:#fff; }
    .btn:hover { filter:brightness(.96); }
    .back-button { margin-bottom:18px; }
    .steps { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:18px; }
    .step { padding:13px 15px; background:#fff; border:1px solid #e2e8f0; border-radius:10px; color:#64748b; font-size:12px; font-weight:700; }
    .step strong { display:block; margin-bottom:5px; color:#1e3a6f; font-size:14px; }
    .step.active { border-color:#93c5fd; background:#eff6ff; }
    .card { padding:22px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 5px 16px rgba(15,23,42,.06); }
    .card + .card { margin-top:18px; }
    .card h2 { margin:0 0 16px; color:#1e3a6f; font-size:17px; }
    .scan-card { border:2px solid #1651A4; box-shadow:0 10px 24px rgba(22,81,164,.12); }
    .scan-card h2 { color:#1C396A; font-size:20px; }
    .scan-badge { display:inline-flex; margin-bottom:10px; padding:5px 9px; border-radius:999px; background:#dbeafe; color:#1d4ed8; font-size:10px; font-weight:900; letter-spacing:.6px; text-transform:uppercase; }
    .field-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
    .field { margin-bottom:15px; }
    label { display:block; margin-bottom:6px; color:#475569; font-size:12px; font-weight:800; }
    input[type=text], input[type=number], input[type=file], select { width:100%; padding:11px 12px; border:1.5px solid #dbe3ee; border-radius:8px; color:#0f172a; font-size:14px; }
    input:focus, select:focus { outline:0; border-color:#2563eb; box-shadow:0 0 0 3px #dbeafe; }
    .scan-row { display:flex; gap:10px; }
    .scan-row input { flex:1; }
    .scan-help { margin:7px 0 0; color:#94a3b8; font-size:11px; }
    .camera { display:none; margin-top:16px; }
    .camera video { display:block; width:100%; max-height:300px; border-radius:9px; background:#0f172a; object-fit:cover; }
    .camera-actions { display:flex; gap:8px; margin-top:9px; }
    .alert { margin-bottom:18px; padding:12px 14px; border-radius:9px; font-size:13px; font-weight:700; }
    .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; }
    .alert-warning { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; }
    .alert-error { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }
    .actions { display:flex; justify-content:flex-end; gap:9px; margin-top:4px; }
    .manual-note { margin-bottom:18px; color:#92400e; font-size:13px; line-height:1.5; }
    .known-item { display:none; align-items:center; gap:12px; margin:10px 0 15px; padding:12px; border:1px solid #bbf7d0; border-radius:9px; background:#f0fdf4; }
    .known-item img { width:48px; height:48px; border-radius:7px; object-fit:cover; background:#dcfce7; }
    .known-item strong { display:block; color:#166534; font-size:14px; }
    .known-item span { color:#15803d; font-size:11px; }
    .manual-client { display:none; }
    @media (max-width:650px) { .intake-heading { display:block; } .intake-heading .btn { margin-top:15px; } .steps, .field-grid { grid-template-columns:1fr; } .scan-row { display:grid; grid-template-columns:1fr auto; } }
</style>

<div class="intake-page">
    <a class="btn btn-light back-button" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff') }}">&larr; Kembali</a>
    <div class="intake-heading">
        <div>
            <div class="eyebrow">Penerimaan inventaris</div>
            <h1>Scan & Input Barang</h1>
            <p class="subtitle">Pilih kategori dan rak, lalu scan barcode untuk memperbarui stok gudang.</p>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif
    @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

    <div class="steps">
        <div class="step active"><strong>1. Tentukan lokasi</strong>Kategori dan rak tujuan</div>
        <div class="step active"><strong>2. Scan barcode</strong>Gunakan kamera atau input kode</div>
        <div class="step"><strong>3. Lengkapi bila baru</strong>Isi data barang yang belum dikenal</div>
    </div>

    <div class="card scan-card">
        <span class="scan-badge">Fitur utama</span>
        <h2>Scan Barcode Barang</h2>
        <p class="subtitle" style="margin:-8px 0 16px;">Arahkan barcode ke kamera atau ketik kode. Data barang yang sudah dikenal akan terisi otomatis.</p>
        <form method="POST" action="{{ route('pencarian.input.proses') }}">
            @csrf
            <div class="field"><label for="kode_barang">Barcode / Kode Barang *</label><div class="scan-row"><input id="kode_barang" type="text" name="kode_barang" value="{{ old('kode_barang', $manualData['kode_barang'] ?? '') }}" placeholder="Scan barcode atau ketik kode barang" autocomplete="off" required><button class="btn btn-dark" type="button" id="camera-button">Buka Kamera</button></div><p class="scan-help" id="scan-status">Scanner siap. Setelah kode terbaca, data akan dicari otomatis.</p><div class="camera" id="camera-box"><video id="camera-video" playsinline></video><div class="camera-actions"><button class="btn btn-red" type="button" id="camera-stop">Tutup Kamera</button></div></div></div>
            <div class="known-item" id="known-item"><img id="known-image" alt="Foto barang"><div><strong id="known-name"></strong><span id="known-meta"></span></div></div>
            <h2 style="margin-top:22px;">Data penerimaan</h2>
            <div class="field-grid">
                <div class="field"><label for="kategori_id">Kategori Barang *</label><select id="kategori_id" name="kategori_id" required><option value="">-- Pilih kategori --</option>@foreach($kategoris as $kategori)<option value="{{ $kategori->id }}" {{ old('kategori_id', $manualData['kategori_id'] ?? '') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }} ({{ $kategori->kode_kategori }})</option>@endforeach</select></div>
                <div class="field"><label for="rak_id">Lokasi Rak Tujuan *</label><select id="rak_id" name="rak_id" required><option value="">-- Pilih rak --</option>@foreach($raks as $rak)<option value="{{ $rak->id }}" {{ old('rak_id', $manualData['rak_id'] ?? '') == $rak->id ? 'selected' : '' }}>{{ $rak->kode_rak }} - {{ $rak->nama_lokasi }}</option>@endforeach</select></div>
            </div>
            <div class="field"><label for="jumlah">Jumlah Masuk *</label><input id="jumlah" type="number" name="jumlah" value="{{ old('jumlah', $manualData['jumlah'] ?? 1) }}" min="1" required></div>
            <div class="actions"><a class="btn btn-light" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff') }}">Batal</a><button class="btn btn-blue" type="submit">Proses Barcode</button></div>
        </form>
    </div>

    @isset($manualData)
    <div class="card">
        <h2>Barang belum dikenali</h2>
        <p class="manual-note">Kode <strong>{{ $manualData['kode_barang'] }}</strong> belum ada di database. Lengkapi data berikut agar barang dibuat dan stok awalnya dicatat di rak yang dipilih.</p>
        <form method="POST" action="{{ route('pencarian.input.manual') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="kategori_id" value="{{ $manualData['kategori_id'] }}"><input type="hidden" name="rak_id" value="{{ $manualData['rak_id'] }}"><input type="hidden" name="kode_barang" value="{{ $manualData['kode_barang'] }}"><input type="hidden" name="jumlah" value="{{ $manualData['jumlah'] }}">
            <div class="field"><label for="nama">Nama Barang *</label><input id="nama" type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Kursi Kantor" required></div>
            <div class="field"><label for="gambar">Foto Barang</label><input id="gambar" type="file" name="gambar" accept="image/*"><p class="scan-help">Opsional, maksimal 2MB.</p></div>
            <div class="actions"><button class="btn btn-blue" type="submit">Simpan Barang Baru</button></div>
        </form>
    </div>
    @endisset

    <div class="card manual-client" id="manual-client">
        <h2>Barcode belum dikenali</h2>
        <p class="manual-note">Kode ini belum ada di database. Lengkapi nama dan foto barang untuk membuat data baru.</p>
        <form method="POST" action="{{ route('pencarian.input.manual') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="kategori_id" id="manual-kategori"><input type="hidden" name="rak_id" id="manual-rak"><input type="hidden" name="kode_barang" id="manual-kode"><input type="hidden" name="jumlah" id="manual-jumlah">
            <div class="field"><label for="manual-nama">Nama Barang *</label><input id="manual-nama" type="text" name="nama" placeholder="Contoh: Kursi Kantor" required></div>
            <div class="field"><label for="manual-gambar">Foto Barang</label><input id="manual-gambar" type="file" name="gambar" accept="image/*"></div>
            <div class="actions"><button class="btn btn-blue" type="submit">Simpan Barang Baru</button></div>
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
