@extends('layouts.app')

@section('content')
<style>
    .page-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:24px; }
    .card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.08); padding:28px; max-width:560px; }
    .form-group { margin-bottom:18px; }
    label { display:block; font-size:13px; font-weight:700; color:#475569; margin-bottom:6px; }
    select, input[type=number], textarea { width:100%; padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; }
    select:focus, input:focus, textarea:focus { outline:none; border-color:#3b82f6; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .form-actions { display:flex; gap:10px; margin-top:24px; }
    .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:13px; }
    .stok-info { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; font-size:14px; margin-top:8px; display:none; }
    .jenis-toggle { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .jenis-btn { padding:12px; border:2px solid #e2e8f0; border-radius:9px; text-align:center; cursor:pointer; font-weight:700; font-size:14px; transition:all .15s; }
    .jenis-btn.masuk.active { border-color:#22c55e; background:#dcfce7; color:#166534; }
    .jenis-btn.keluar.active { border-color:#ef4444; background:#fee2e2; color:#991b1b; }
    input[type=radio] { display:none; }
</style>

<!-- JUDUL -->
<h1 class="page-title">Catat Mutasi Barang</h1>

<!-- CARD DENGAN FORM -->
<div class="card">
    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('mutasi-barang.store') }}">
        @csrf

        <div class="form-group">
            <label>Barang <span style="color:#ef4444">*</span></label>
            <select name="barang_id" id="barang-select" required onchange="updateStokInfo()">
                <option value="">-- Pilih Barang --</option>
                @foreach($barangs as $b)
                    <option value="{{ $b->id }}"
                        data-stok="{{ $b->stok }}"
                        data-satuan="{{ $b->satuan }}"
                        data-nama="{{ $b->nama }}"
                        {{ old('barang_id', request('barang_id')) == $b->id ? 'selected' : '' }}>
                        {{ $b->nama }} ({{ $b->kategori->nama }})
                    </option>
                @endforeach
            </select>
            <div class="stok-info" id="stok-info"></div>
        </div>

        <div class="form-group">
            <label>Jenis Mutasi <span style="color:#ef4444">*</span></label>
            <div class="jenis-toggle">
                <label class="jenis-btn masuk {{ old('jenis') === 'masuk' ? 'active' : '' }}" onclick="setJenis('masuk')">
                    <input type="radio" name="jenis" value="masuk" {{ old('jenis') === 'masuk' ? 'checked' : '' }}>
                    ↑ Barang Masuk
                </label>
                <label class="jenis-btn keluar {{ old('jenis') === 'keluar' ? 'active' : '' }}" onclick="setJenis('keluar')">
                    <input type="radio" name="jenis" value="keluar" {{ old('jenis') === 'keluar' ? 'checked' : '' }}>
                    ↓ Barang Keluar
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Jumlah <span style="color:#ef4444">*</span></label>
            <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" rows="3" placeholder="Contoh: Pembelian dari supplier, Digunakan untuk kegiatan X...">{{ old('keterangan') }}</textarea>
        </div>

        <!-- SATU-SATUNYA form-actions -->
        <div class="form-actions">
    @if(request('barang_id'))
        <a href="{{ route('barang.show', request('barang_id')) }}" class="btn btn-secondary">Batal</a>
    @elseif(request('from') === 'dashboard')
        <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn-secondary">Batal</a>
    @else
        <a href="{{ route('mutasi-barang.index') }}" class="btn btn-secondary">Batal</a>
    @endif
    <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
</div>
    </form>
</div>

<script>
function updateStokInfo() {
    const sel = document.getElementById('barang-select');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('stok-info');
    if (sel.value) {
        info.style.display = 'block';
        info.innerHTML = `Stok saat ini: <strong>${opt.dataset.stok} ${opt.dataset.satuan}</strong>`;
    } else {
        info.style.display = 'none';
    }
}
function setJenis(jenis) {
    document.querySelectorAll('.jenis-btn').forEach(el => el.classList.remove('active'));
    document.querySelector(`.jenis-btn.${jenis}`).classList.add('active');
    document.querySelector(`input[value="${jenis}"]`).checked = true;
}
// Init on load
document.addEventListener('DOMContentLoaded', updateStokInfo);
</script>
@endsection
