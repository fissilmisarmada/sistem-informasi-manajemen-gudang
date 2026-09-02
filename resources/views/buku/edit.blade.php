@extends('layouts.app')

@section('content')
    <div style="padding:24px 0;">
        <div style="max-width:720px; margin:0 auto; background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 8px 24px rgba(15,23,42,0.06); padding:28px;">
            <h1 style="margin:0 0 20px; font-size:30px; color:#0f172a;">Edit Buku</h1>

            <form method="POST" action="{{ route('buku.update-data', $buku) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:grid; gap:16px;">
                    <div>
                        <label for="kode_buku" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">Kode Buku</label>
                        <input type="text" name="kode_buku" id="kode_buku" value="{{ old('kode_buku', $buku->kode_buku) }}" required style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div>
                        <label for="judul" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">Judul</label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul', $buku->judul) }}" required style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div>
                        <label for="cover" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">Cover Buku</label>
                        @if($buku->cover)
                            <img src="{{ asset('storage/' . $buku->cover) }}" alt="Cover {{ $buku->judul }}" style="width:70px;height:90px;object-fit:cover;border-radius:8px;display:block;margin-bottom:8px;">
                        @endif
                        <input type="file" name="cover" id="cover" accept="image/jpeg,image/png,image/webp" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div>
                        <label for="kategori" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">Kategori</label>
                        <input type="text" name="kategori" id="kategori" value="{{ old('kategori', $buku->kategori) }}" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div>
                        <label for="isbn" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">ISBN</label>
                        <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $buku->isbn) }}" required style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div>
                        <label for="eisbn" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">EISBN (opsional)</label>
                        <input type="text" name="eisbn" id="eisbn" value="{{ old('eisbn', $buku->eisbn) }}" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div>
                        <label for="jumlah_halaman" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">Jumlah Halaman</label>
                        <input type="number" name="jumlah_halaman" id="jumlah_halaman" value="{{ old('jumlah_halaman', $buku->jumlah_halaman) }}" min="1" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div>
                        <label for="stok" style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">Jumlah Stok</label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', $buku->stok) }}" min="0" required style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; box-sizing:border-box;">
                    </div>

                    <div style="display:flex; gap:12px; margin-top:8px;">
                        <button type="submit" style="padding:12px 18px; background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff; border:none; border-radius:10px; font-weight:700; cursor:pointer;">Update</button>
                        <a href="{{ route('buku.index') }}" style="padding:12px 18px; background:#e2e8f0; color:#0f172a; border-radius:10px; text-decoration:none; font-weight:700;">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
