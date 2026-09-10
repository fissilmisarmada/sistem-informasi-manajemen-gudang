@extends('layouts.app')

@section('content')
<style>
    .intake-page {
        max-width: 880px;
        margin: 0 auto;
        padding: 24px 16px 48px;
    }
    .back-button {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 14px; border-radius: 999px; background: var(--andon-panel);
        border: 1px solid var(--andon-line-strong); color: var(--andon-ink); font-size: 13px; font-weight: 700;
        text-decoration: none; transition: all .18s ease;
        box-shadow: var(--andon-shadow); margin-bottom: 22px;
    }
    .back-button:hover { border-color: var(--andon-ink); transform: translateY(-1px); color: var(--andon-ink); }
    .intake-heading { margin-bottom: 22px; }
    .eyebrow { display: inline-block; padding: 4px 10px; background: var(--andon-amber); color: var(--andon-amber-ink); border-radius: 999px; font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 10px; border: 1px solid #E6C200; }
    h1 { margin: 0; color: var(--andon-ink); font-size: 26px; font-weight: 800; letter-spacing: -.03em; }
    .subtitle { margin: 6px 0 0; color: var(--andon-muted); font-size: 13px; }
    .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 22px; }
    .step { padding: 14px 16px; background: var(--andon-panel); border: 1px solid #EDEEF2; border-radius: 20px; color: var(--andon-muted); font-size: 12px; font-weight: 600; box-shadow: 0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); transition: all .18s; }
    .step strong { display: block; margin-bottom: 4px; color: var(--andon-ink); font-size: 13px; font-weight: 800; letter-spacing:-.01em; }
    .step.active { border-color: var(--andon-ink); background: #FBFBFD; }
    .step.active strong { color: var(--andon-ink); }
    .card { padding: 22px; background: var(--andon-panel); border: 1px solid #EDEEF2; border-radius: 20px; box-shadow: 0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); margin-bottom: 18px; }
    .card h2 { margin: 0 0 16px; color: var(--andon-ink); font-size: 16px; font-weight: 800; letter-spacing: -.02em; display: flex; align-items: center; gap: 8px; }
    .scan-card { border-color: rgba(247,214,10,.55); background: var(--andon-panel); box-shadow: var(--andon-shadow); position: relative; overflow: hidden; }
    .scan-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--andon-amber); }
    .scan-badge { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; padding: 4px 10px; border-radius: 999px; background: var(--andon-amber); color: var(--andon-amber-ink); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; border: 1px solid #E6C200; }
    .field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .field { margin-bottom: 16px; }
    label { display: block; margin-bottom: 8px; color: var(--andon-muted); font-size: 11px; font-weight: 700; letter-spacing:.06em; }
    input[type=text], input[type=number], input[type=file], select {
        width: 100%; padding: 12px 14px; border: 1px solid #E8EAF0; border-radius: 14px; color: var(--andon-ink); font-size: 14px; background: #FBFBFD;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease; box-sizing: border-box;
    }
    input[type=text]:focus, input[type=number]:focus, input[type=file]:focus, select:focus { outline: none; border-color: var(--andon-ink); box-shadow: 0 0 0 3px rgba(15,23,42,.06); background: #fff; }
    .scan-row { display: flex; gap: 10px; align-items: center; }
    .scan-row input { flex: 1; font-weight: 600; }
    .scan-help { margin: 8px 0 0; color: var(--andon-muted); font-size: 12px; display: flex; align-items: center; gap: 6px; }
    /* legacy btn aliases mapped to andon */
    .btn-blue, .btn-dark { background: var(--andon-ink); color: #fff; border-color: var(--andon-ink); }
    .btn-blue:hover, .btn-dark:hover { background: var(--andon-navy-2); }
    .btn-light { background: var(--andon-panel); color: var(--andon-ink); border: 1px solid var(--andon-line-strong); }
    .btn-light:hover { border-color: var(--andon-ink); }
    .btn-red { background: var(--andon-panel); color: var(--andon-red); border: 1px solid var(--andon-line-strong); }
    .btn-red:hover { border-color: var(--andon-red); background: #FEF2F2; }
    .camera { display: none; margin-top: 16px; position: relative; border-radius: 20px; overflow: hidden; background: var(--andon-ink); border: 1px solid #E8EAF0; box-shadow: 0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .camera video { display: block; width: 100%; max-height: 320px; object-fit: cover; }
    .camera-actions { position: absolute; bottom: 12px; right: 12px; }
    .known-item { display: none; align-items: center; gap: 14px; margin: 16px 0 18px; padding: 14px; border: 1px solid #EDEEF2; border-radius: 20px; background: #FBFBFD; box-shadow: 0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .known-item img { width: 52px; height: 52px; border-radius: 12px; object-fit: cover; background: #F1F5F9; border: 1px solid var(--andon-line); }
    .known-item strong { display: block; color: var(--andon-ink); font-size: 14px; font-weight: 800; margin-bottom: 2px; }
    .known-item span { color: var(--andon-muted); font-size: 12px; font-weight: 600; }
    .alert { margin-bottom: 18px; padding: 12px 14px; border-radius: 12px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px; border: 1px solid var(--andon-line); }
    .alert-success { background: #F0FDF4; border-color: #BBF7D0; color: #14532D; }
    .alert-warning { background: #FFFBEB; border-color: #FDE68A; color: #92400E; }
    .alert-error { background: #FEF2F2; border-color: #FECACA; color: #7F1D1D; }
    .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 12px; }
    .manual-note { margin-bottom: 16px; padding: 12px 14px; background: #FFFBEB; border: 1px solid rgba(247,214,10,.5); border-radius: 12px; color: #92400E; font-size: 13px; line-height: 1.5; }
    .manual-client { display: none; }
    @media (max-width: 650px) {
        .intake-heading { display: block; }
        .steps, .field-grid { grid-template-columns: 1fr; }
        .scan-row { flex-direction: column; align-items: stretch; }
        .actions { flex-direction: column-reverse; }
        .actions .btn { width: 100%; justify-content: center; }
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

    <!-- Scanner Section -->
    <div class="card scan-card">
        <span class="scan-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z" /></svg>
            Fitur Utama
        </span>
        <h2>Scan Barcode Barang</h2>

        <form method="POST" action="{{ route('pencarian.input.proses') }}">
            @csrf
            <div class="field">
                <label for="kode_barang">Barcode / Kode Barang *</label>
                <div class="scan-row">
                    <input id="kode_barang" type="text" name="kode_barang" value="{{ old('kode_barang', $manualData['kode_barang'] ?? '') }}" placeholder="Scan barcode atau ketik kode barang" autocomplete="off" required>
                    <button class="btn btn--amber" type="button" id="camera-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px; height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                        Buka Kamera
                    </button>
                </div>
                <p class="scan-help" id="scan-status">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                    Scanner siap. Setelah kode terbaca, data akan dicari otomatis.
                </p>
                <div class="camera" id="camera-box">
                    <video id="camera-video" autoplay muted playsinline></video>
                    <div id="html5-reader" style="display:none"></div>
                    <div class="camera-actions">
                        <button class="btn btn--ghost" type="button" id="camera-stop">Tutup Kamera</button>
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

            <h2 style="margin-top:22px;">Data Penerimaan</h2>
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
                <a class="btn btn--ghost" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff') }}">Batal</a>
                <button class="btn btn--primary" type="submit">Proses Barcode</button>
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
                <button class="btn btn--primary" type="submit">Simpan Barang Baru</button>
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
                <button class="btn btn--primary" type="submit">Simpan Barang Baru</button>
            </div>
        </form>
    </div>
</div>

<script type="module">
import { startCamera, stopStream, createDetector, scanWithDetector, scanWithHtml5Qrcode, isSecureContextOk } from '/js/barcode-scanner.js';

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
let lookupTimer, cameraStream = null, stopScan = null;

async function lookupCode() {
    const code = codeInput.value.trim();
    if (!code) { knownItem.style.display='none'; manualClient.style.display='none'; scanStatus.textContent='Scanner siap. Setelah kode terbaca, data akan dicari otomatis.'; return; }
    scanStatus.textContent='Mencari data barang...';
    try {
        const response = await fetch(`{{ route('pencarian.input.lookup') }}?kode_barang=${encodeURIComponent(code)}`, { headers:{Accept:'application/json'} });
        const result = await response.json();
        if (result.found) {
            categorySelect.value = result.barang.kategori_id;
            knownName.textContent = result.barang.nama;
            knownMeta.textContent = `${result.barang.kategori || 'Tanpa kategori'} · Stok ${result.barang.stok} ${result.barang.satuan}`;
            knownImage.src = result.barang.gambar || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="48" height="48"%3E%3Crect width="100%25" height="100%25" fill="%23F8FAFC"/%3E%3C/svg%3E';
            knownItem.style.display='flex'; manualClient.style.display='none';
            scanStatus.textContent='Barang dikenali. Tentukan rak dan jumlah masuk.';
        } else {
            knownItem.style.display='none'; manualClient.style.display='block';
            document.getElementById('manual-kategori').value=categorySelect.value;
            document.getElementById('manual-rak').value=rackSelect.value;
            document.getElementById('manual-kode').value=code;
            document.getElementById('manual-jumlah').value=quantityInput.value;
            scanStatus.textContent='Barcode belum dikenali. Lengkapi data barang baru di bawah.';
        }
    } catch { scanStatus.textContent='Gagal mencari data. Periksa koneksi.'; }
}
codeInput.addEventListener('input', () => { clearTimeout(lookupTimer); lookupTimer=setTimeout(lookupCode,400); });
codeInput.addEventListener('change', lookupCode);
[categorySelect, rackSelect].forEach(el=>el.addEventListener('change', syncManualData));
quantityInput.addEventListener('input', syncManualData);
function syncManualData(){ const m=document.getElementById('manual-kategori'); if(!m) return; m.value=categorySelect.value; document.getElementById('manual-rak').value=rackSelect.value; document.getElementById('manual-kode').value=codeInput.value.trim(); document.getElementById('manual-jumlah').value=quantityInput.value; }

function onBarcodeDetected(value){
    codeInput.value = value;
    scanStatus.textContent = 'Barcode terbaca: '+value+' — mencari data...';
    stopCamera(); lookupCode();
}

async function startCameraFlow(){
    if (!isSecureContextOk()) { scanStatus.textContent='Kamera butuh HTTPS atau buka via http://localhost . Sekarang: '+location.protocol+'//'+location.host; codeInput.focus(); return; }
    if (!navigator.mediaDevices?.getUserMedia) { scanStatus.textContent='Browser tidak dukung kamera. Ketik barcode manual.'; codeInput.focus(); return; }
    cameraButton.disabled=true; scanStatus.textContent='Membuka kamera...';
    try {
        const detector = await createDetector();
        if (detector) {
            cameraStream = await startCamera(cameraVideo);
            cameraBox.style.display='block'; cameraButton.style.display='none';
            scanStatus.textContent='Arahkan barcode ke kamera. Pastikan cukup cahaya.';
            stopScan = await scanWithDetector(cameraVideo, onBarcodeDetected, ()=>{});
        } else {
            // fallback html5-qrcode
            cameraBox.style.display='block'; cameraButton.style.display='none';
            cameraVideo.style.display='none';
            document.getElementById('html5-reader').style.display='block';
            scanStatus.textContent='Memuat scanner...';
            stopScan = await scanWithHtml5Qrcode('html5-reader', onBarcodeDetected, ()=>{});
            scanStatus.textContent='Arahkan barcode ke kamera.';
        }
    } catch (e) {
        const msg = e?.message || String(e);
        if (msg.includes('NotAllowed') || msg.includes('Permission')) scanStatus.textContent='Izin kamera ditolak. Aktifkan di pengaturan browser.';
        else if (msg.includes('NotFound')) scanStatus.textContent='Kamera tidak ditemukan di perangkat ini.';
        else if (msg.includes('NO_DETECTOR')) scanStatus.textContent='Scanner tidak didukung, ketik barcode manual.';
        else scanStatus.textContent='Kamera gagal: '+msg;
        stopCamera(); cameraButton.disabled=false;
    }
}
function stopCamera(){
    try{ stopScan?.(); }catch{} stopScan=null;
    stopStream(cameraStream, cameraVideo); cameraStream=null;
    cameraBox.style.display='none'; cameraButton.style.display='inline-flex'; cameraButton.disabled=false;
    cameraVideo.style.display='block'; document.getElementById('html5-reader').style.display='none';
}
cameraButton.addEventListener('click', startCameraFlow);
cameraStop.addEventListener('click', stopCamera);
window.addEventListener('beforeunload', stopCamera);
document.addEventListener('visibilitychange', ()=>{ if(document.hidden) stopCamera(); });
</script>
@endsection
