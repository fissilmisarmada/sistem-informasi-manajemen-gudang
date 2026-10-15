@extends('layouts.app')

@section('content')
<style>
    .edit-page{max-width:760px;margin:0 auto}
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
    .book-fieldset{border:1px solid #E8EAF0;border-radius:14px;padding:14px 14px 2px;margin:0 0 16px;background:#FBFBFD}
    .book-fieldset legend{font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--andon-muted);padding:0 6px}
    .current-img{width:120px;height:90px;object-fit:cover;border-radius:10px;margin-bottom:8px;display:block;border:1px solid var(--andon-line)}
    @media(max-width:600px){.form-row{grid-template-columns:1fr;gap:0}}
    @media(max-width:420px){.form-actions{flex-direction:column-reverse}.form-actions .btn{width:100%;justify-content:center}}
</style>

<div class="edit-page">
<div class="page-head">
    <a href="{{ route('barang.show', $barang) }}" class="back-link" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
    <h1>Edit Barang</h1>
</div>

<div class="card">
    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0;padding-left:18px">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('barang.update', $barang) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label>Kode Barang <span class="req">*</span></label>
                <input class="andon-control" type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" required>
            </div>
            <div class="form-group">
                <label>Kategori <span class="req">*</span></label>
                <select class="andon-control" name="kategori_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id', $barang->kategori_id) == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Nama Barang <span class="req">*</span></label>
            <input class="andon-control" type="text" name="nama" value="{{ old('nama', $barang->nama) }}" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Satuan <span class="req">*</span></label>
                <input class="andon-control" type="text" name="satuan" value="{{ old('satuan', $barang->satuan) }}" required>
            </div>
            <div class="form-group">
                <label>Lokasi Rak</label>
                <select class="andon-control" name="rak_id">
                    <option value="">-- Tanpa Rak --</option>
                    @foreach($raks as $rak)
                        <option value="{{ $rak->id }}" {{ old('rak_id', $barang->rak_id) == $rak->id ? 'selected' : '' }}>{{ $rak->kode_rak }} – {{ $rak->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <p class="hint">Stok minimum otomatis: <strong style="color:var(--andon-ink)">{{ \App\Models\Barang::stokMinimumForKategori($barang->kategori?->kode_kategori) }} {{ $barang->satuan }}</strong> (Furnitur = 0, lainnya = 10) · Stok saat ini: <strong style="color:var(--andon-ink)">{{ $barang->stok }} {{ $barang->satuan }}</strong> · ubah stok lewat Mutasi.</p>

        <div class="form-group">
            <label>Gambar Barang</label>
            @if($barang->gambar)
                <img src="{{ Storage::url($barang->gambar) }}" alt="{{ $barang->nama }}" class="current-img">
                <p class="hint">Upload gambar baru untuk mengganti yang sekarang.</p>
            @endif
            <input class="andon-control" type="file" name="gambar" accept="image/*">
        </div>

        @php
            $rawKet = $barang->keterangan;
            if (is_string($rawKet)) { $d = json_decode($rawKet, true); if (is_array($d)) $rawKet = $d; }
            $ketArr = is_array($rawKet) ? $rawKet : [];
            // legacy: keterangan pernah simpan string json langsung — catatan di $ketArr['catatan'] agar tidak hilang
            $catatanFallback = is_string($barang->keterangan) && !is_array($rawKet) ? $barang->keterangan : ($ketArr['catatan'] ?? $ketArr['keterangan'] ?? '');
        @endphp
        <fieldset class="book-fieldset">
            <legend>Detail Buku <span style="font-weight:600;color:var(--andon-faint)">(opsional)</span></legend>
            <div class="form-row">
                <div class="form-group">
                    <label>ISBN</label>
                    <input class="andon-control" type="text" name="isbn" value="{{ old('isbn', $ketArr['isbn'] ?? '') }}" placeholder="9786021234002" maxlength="30">
                </div>
                <div class="form-group">
                    <label>Jumlah Halaman</label>
                    <input class="andon-control" type="number" name="jumlah_halaman" value="{{ old('jumlah_halaman', $ketArr['jumlah_halaman'] ?? '') }}" min="1" max="99999" placeholder="168">
                </div>
            </div>
            <div class="form-group">
                <label>Kategori Asli / Penulis</label>
                <input class="andon-control" type="text" name="kategori_asli" value="{{ old('kategori_asli', $ketArr['kategori_asli'] ?? '') }}" placeholder="Manajemen, Teknologi Informasi...">
            </div>
            <div class="form-group" style="margin-bottom:0">
                <label>Catatan / Keterangan Umum</label>
                <textarea class="andon-control" name="keterangan" rows="2" placeholder="Deskripsi atau catatan tambahan...">{{ old('keterangan', $catatanFallback) }}</textarea>
            </div>
        </fieldset>

        <div class="form-actions">
            <a href="{{ route('barang.show', $barang) }}" class="btn btn--ghost">Batal</a>
            <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
</div>
@endsection
