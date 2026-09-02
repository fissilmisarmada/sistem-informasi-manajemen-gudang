@extends('layouts.app')

@section('content')
    <a class="back-dashboard" href="{{ route('denah-gudang', ['rak' => $rak->id]) }}">&larr; Kembali</a>
    <h1>Detail Rak {{ $rak->kode_rak }}</h1>

    <div style="margin-bottom:16px; padding:12px; border:1px solid #d1d5db; border-radius:8px; background:#f9fafb;">
        <p><strong>Nama Lokasi:</strong> {{ $rak->nama_lokasi }}</p>
        <p><strong>Kapasitas:</strong> {{ $rak->kapasitas ?? '-' }}</p>
    </div>

    <h3>Daftar Buku di Rak</h3>
    @if($buku->isEmpty())
        <p>Belum ada buku yang ditempatkan di rak ini.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse">
            <thead>
                <tr>
                    <th>Kode Buku</th>
                    <th>Judul</th>
                    <th>ISBN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buku as $item)
                    <tr>
                        <td>{{ $item->kode_buku }}</td>
                        <td>{{ $item->judul }}</td>
                        <td>{{ $item->isbn }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
