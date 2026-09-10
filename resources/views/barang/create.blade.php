@extends('layouts.app')

@section('content')
<style>
    .create-page{max-width:760px;margin:0 auto}
    .page-head{max-width:680px;margin:0 auto 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
    .page-head h1{margin:0;font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
    .card{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);padding:28px;max-width:680px;margin:0 auto;position:relative;overflow:hidden;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .card:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .form-group{margin-bottom:16px}
    label{display:block;font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--andon-muted);margin-bottom:8px}
    label span.req{color:var(--andon-red)}
    .andon-control{width:100%;padding:12px 14px;border:1px solid #E8EAF0;border-radius:14px;font-size:14px;color:var(--andon-ink);background:#FBFBFD;outline:none;transition:border-color .18s,box-shadow .18s,background .18s}
    .andon-control:focus{border-color:var(--andon-ink);box-shadow:0 0 0 3px rgba(15,23,42,.06);background:#fff}
    textarea.andon-control{resize:vertical;min-height:88px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .form-actions{display:flex;gap:10px;margin-top:20px;flex-wrap:wrap}
    .hint{font-size:11px;color:var(--andon-faint);margin-top:6px;font-weight:600}
    .alert-error{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;padding:10px 14px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:600}
    @media(max-width:600px){.form-row{grid-template-columns:1fr;gap:0}}
    @media(max-width:420px){.form-actions{flex-direction:column-reverse}.form-actions .btn{width:100%;justify-content:center}}
</style>

<div class="create-page">
<div class="page-head">
    <a href="{{ route('barang.index') }}" class="btn btn--ghost" style="min-height:36px;padding:0 14px;font-size:12px">← Kembali</a>
    <h1>Tambah Barang Baru</h1>
</div>

<div class="card">
    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0;padding-left:18px">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('barang.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>Kode Barang <span class="req">*</span></label>
                <input class="andon-control" type="text" name="kode_barang" value="{{ old('kode_barang') }}" placeholder="Contoh: ATK-001" required>
            </div>
            <div class="form-group">
                <label>Kategori <span class="req">*</span></label>
                <select class="andon-control" name="kategori_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Nama Barang <span class="req">*</span></label>
            <input class="andon-control" type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap barang" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Satuan <span class="req">*</span></label>
                <input class="andon-control" type="text" name="satuan" value="{{ old('satuan', 'pcs') }}" placeholder="pcs, rim, unit, lusin..." required>
            </div>
            <div class="form-group">
                <label>Lokasi Rak</label>
                <select class="andon-control" name="rak_id">
                    <option value="">-- Tanpa Rak --</option>
                    @foreach($raks as $rak)
                        <option value="{{ $rak->id }}" {{ old('rak_id') == $rak->id ? 'selected' : '' }}>{{ $rak->kode_rak }} – {{ $rak->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Stok Awal <span class="req">*</span></label>
                <input class="andon-control" type="number" name="stok" value="{{ old('stok', 0) }}" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok Minimum</label>
                <input class="andon-control" type="number" name="stok_minimum" value="{{ old('stok_minimum', 0) }}" min="0">
                <p class="hint">Alert muncul jika stok ≤ nilai ini</p>
            </div>
        </div>

        <div class="form-group">
            <label>Gambar Barang</label>
            <input class="andon-control" type="file" name="gambar" accept="image/*">
            <p class="hint">Opsional · maks 2MB (JPG, PNG, GIF, WebP)</p>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="andon-control" name="keterangan" rows="3" placeholder="Deskripsi atau catatan tambahan...">{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('barang.index') }}" class="btn btn--ghost">Batal</a>
            <button type="submit" class="btn btn--primary">Simpan Barang</button>
        </div>
    </form>
</div>
</div>
@endsection
