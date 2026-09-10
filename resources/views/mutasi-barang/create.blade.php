@extends('layouts.app')

@section('content')
<style>
    .mutation-page { max-width:720px; margin:0 auto; }
    .card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); padding:28px; max-width:560px; margin:0 auto; transition: box-shadow .22s cubic-bezier(.16,1,.3,1); }
    .card:hover { box-shadow:0 10px 28px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.05); }
    .form-group { margin-bottom:18px; }
    label { display:block; font-size:11px; font-weight:700; color:var(--andon-muted); margin-bottom:8px; letter-spacing:.06em; }
    select, input[type=number], textarea { width:100%; padding:12px 14px; border:1px solid #E8EAF0; border-radius:14px; font-size:14px; background:#FBFBFD; color:var(--andon-ink); box-sizing:border-box; transition: border-color .18s ease, box-shadow .18s ease, background .18s ease; }
    select:focus, input:focus, textarea:focus { outline:none; border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); background:#fff; }
    .form-actions { display:flex; gap:10px; margin-top:24px; }
    .alert-error { background:#FEF2F2; color:#7F1D1D; border:1px solid #FECACA; padding:10px 14px; border-radius:14px; margin-bottom:16px; font-size:13px; }
    .stok-info { background:#FBFBFD; border:1px solid #EDEEF2; border-radius:14px; padding:11px 14px; font-size:13px; margin-top:10px; display:none; color:var(--andon-muted); }
    .jenis-toggle { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .jenis-btn { padding:13px; border:1px solid #E8EAF0; border-radius:14px; text-align:center; cursor:pointer; font-weight:800; font-size:13px; transition: all .2s cubic-bezier(.16,1,.3,1); background:#FBFBFD; color:var(--andon-muted); }
    .jenis-btn:hover { border-color: var(--andon-line-strong); transform: translateY(-1px); }
    .jenis-btn.masuk.active { border-color: var(--andon-green); background: #F0FDF4; color: #14532D; box-shadow: 0 4px 12px rgba(22,163,74,.12); }
    .jenis-btn.keluar.active { border-color: var(--andon-red); background: #FEF2F2; color: #7F1D1D; box-shadow: 0 4px 12px rgba(220,38,38,.1); }
    input[type=radio] { display:none; }
</style>

<div class="mutation-page">
<!-- JUDUL -->
<h1 class="andon-page-title">Catat Mutasi Barang</h1>

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
            <label>Barang <span style="color:var(--andon-red)">*</span></label>
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
            <label>Jenis Mutasi <span style="color:var(--andon-red)">*</span></label>
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
            <label>Jumlah <span style="color:var(--andon-red)">*</span></label>
            <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" rows="3" placeholder="Contoh: Pembelian dari supplier, Digunakan untuk kegiatan X...">{{ old('keterangan') }}</textarea>
        </div>

        <!-- SATU-SATUNYA form-actions -->
        <div class="form-actions">
    @php
        $cancelUrl = request('barang_id')
            ? route('barang.show', request('barang_id'))
            : (request('from') === 'dashboard'
                ? (auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')))
                : route('mutasi-barang.index'));
    @endphp
    <a href="{{ $cancelUrl }}" class="btn btn--ghost">Batal</a>
    <button type="submit" class="btn btn--primary">Simpan Mutasi</button>
</div>
    </form>
</div>
</div>

<script>
function updateStokInfo() {
    const sel = document.getElementById('barang-select');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('stok-info');
    if (sel.value) {
        info.style.display = 'block';
        info.innerHTML = `Stok saat ini: <strong style="color:var(--andon-ink)">${opt.dataset.stok} ${opt.dataset.satuan}</strong>`;
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
