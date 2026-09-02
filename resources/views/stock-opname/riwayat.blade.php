@extends('layouts.app')

@section('content')
    <a class="back-dashboard" href="{{ route('stock-opname.index') }}">&larr; Kembali</a>
    <h1>Riwayat Stock Opname Rak {{ $rak->kode_rak }}</h1>

    @if($riwayat->isEmpty())
        <p>Belum ada riwayat stock opname untuk rak ini.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah Tercatat</th>
                    <th>Jumlah Fisik</th>
                    <th>Selisih</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $item)
                    <tr>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->jumlah_tercatat }}</td>
                        <td>{{ $item->jumlah_fisik }}</td>
                        <td>{{ $item->selisih }}</td>
                        <td>{{ $item->staff->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
