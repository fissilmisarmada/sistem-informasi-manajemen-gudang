@extends('layouts.app')

@section('content')
<style>
    .page-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:24px; }
    .card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.08); padding:28px; max-width:680px; }
    .form-group { margin-bottom:18px; }
    label { display:block; font-size:13px; font-weight:700; color:#475569; margin-bottom:6px; }
    input[type=text], input[type=number], input[type=file], select, textarea {
        width:100%; padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px;
    }
    input:focus, select:focus, textarea:focus { outline:none; border-color:#3b82f6; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-primary:hover { background:#2563eb; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .btn-secondary:hover { background:#cbd5e1; }
    .form-actions { display:flex; gap:10px; margin-top:24px; }
    .hint { font-size:12px; color:#94a3b8; margin-top:4px; }
    .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:13px; }
</style>

<h1 class="page-title">Tambah Barang Baru</h1>

<div class="card">
    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('barang.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>Kode Barang <span style="color:#ef4444">*</span></label>
                <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" placeholder="Contoh: ATK-001" required>
            </div>
            <div class="form-group">
                <label>Kategori <span style="color:#ef4444">*</span></label>
                <select name="kategori_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Nama Barang <span style="color:#ef4444">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap barang" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Satuan <span style="color:#ef4444">*</span></label>
                <input type="text" name="satuan" value="{{ old('satuan', 'pcs') }}" placeholder="pcs, rim, unit, lusin..." required>
            </div>
            <div class="form-group">
                <label>Lokasi Rak</label>
                <select name="rak_id">
                    <option value="">-- Tanpa Rak --</option>
                    @foreach($raks as $rak)
                        <option value="{{ $rak->id }}" {{ old('rak_id') == $rak->id ? 'selected' : '' }}>{{ $rak->kode_rak }} – {{ $rak->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Stok Awal <span style="color:#ef4444">*</span></label>
                <input type="number" name="stok" value="{{ old('stok', 0) }}" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok Minimum</label>
                <input type="number" name="stok_minimum" value="{{ old('stok_minimum', 0) }}" min="0">
                <p class="hint">Alert akan muncul jika stok ≤ nilai ini</p>
            </div>
        </div>

        <div class="form-group">
            <label>Gambar Barang</label>
            <input type="file" name="gambar" accept="image/*">
            <p class="hint">Opsional. Maks 2MB (JPG, PNG, GIF, WebP)</p>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" rows="3" placeholder="Deskripsi atau catatan tambahan...">{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Barang</button>
        </div>
    </form>
</div>
@endsection
