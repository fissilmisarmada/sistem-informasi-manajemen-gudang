@extends('layouts.app')

@section('content')
    <a class="back-dashboard" href="{{ route('stock-opname.index') }}">&larr; Kembali</a>
    <h1>Stock Opname Rak {{ $rak->kode_rak }}</h1>

    <div style="margin-bottom:16px;padding:12px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;">
        <p><strong>Lokasi:</strong> {{ $rak->nama_lokasi }}</p>
        <p><strong>Jumlah Tercatat Sistem:</strong> {{ $jumlahTercatat }}</p>
    </div>

    <form method="POST" action="{{ route('stock-opname.store', $rak) }}">
        @csrf
        <div style="max-width:400px;display:grid;gap:12px;">
            <label for="jumlah_fisik">Jumlah Fisik</label>
            <input type="number" name="jumlah_fisik" id="jumlah_fisik" min="0" required style="padding:10px;border:1px solid #d1d5db;border-radius:6px">
            <button type="submit" style="padding:10px 16px;background:#111827;color:#fff;border:none;border-radius:6px;cursor:pointer">Simpan Stock Opname</button>
        </div>
    </form>
@endsection
