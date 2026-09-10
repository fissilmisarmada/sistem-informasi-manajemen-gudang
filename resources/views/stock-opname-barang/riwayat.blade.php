@extends('layouts.app')

@section('content')
<style>
    .back-link { display:inline-flex; align-items:center; gap:8px; margin-bottom:20px; padding:9px 14px; background:#e2e8f0; color:#0f172a; border-radius:9px; font-weight:700; font-size:14px; text-decoration:none; }
    .back-link:hover { background:#cbd5e1; }
    .page-sub { font-size:14px; color:var(--andon-muted); margin-bottom:24px; }
    .card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); padding:24px; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:12px 14px; text-align:left; font-size:13px; font-weight:700; color:#475569; }
    td { padding:12px 14px; border-bottom:1px solid #f1f5f9; font-size:14px; }
    tr:last-child td { border-bottom:none; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .empty-state { text-align:center; padding:40px; color:#94a3b8; }
</style>

<a href="{{ request('from') === 'dashboard' ? (auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff')) : route('stock-opname-barang.index') }}" class="back-link">← Kembali</a>

<h1 class="andon-page-title" style="margin-bottom:6px">Riwayat Opname: {{ $barang->nama }}</h1>
<p class="page-sub">{{ $barang->kode_barang }} · {{ $barang->kategori->nama }}</p>

<div class="card">
    @if($riwayat->isEmpty())
        <div class="empty-state">Belum ada riwayat opname untuk barang ini.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tercatat</th>
                    <th>Fisik</th>
                    <th>Selisih</th>
                    <th>Keterangan</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $r)
                <tr>
                    <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $r->jumlah_tercatat }} {{ $barang->satuan }}</td>
                    <td>{{ $r->jumlah_fisik }} {{ $barang->satuan }}</td>
                    <td>
                        @if($r->selisih == 0)
                            <span class="badge badge-green">Sesuai</span>
                        @else
                            <span class="badge badge-red">{{ $r->selisih > 0 ? '+' : '' }}{{ $r->selisih }}</span>
                        @endif
                    </td>
                    <td>{{ $r->keterangan ?? '-' }}</td>
                    <td>{{ $r->staff->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $riwayat->links() }}</div>
    @endif
</div>
@endsection
