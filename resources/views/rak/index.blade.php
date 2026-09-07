@extends('layouts.app')

@section('content')
<style>
    .rack-page { max-width:1120px; margin:0 auto; padding:10px 0 36px; }
    .rack-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; margin-bottom:22px; }
    .eyebrow { color:#64748b; font-size:11px; font-weight:800; letter-spacing:1.1px; text-transform:uppercase; }
    h1 { margin:7px 0 0; color:#123b82; font-size:31px; }
    .heading-copy p { margin:6px 0 0; color:#64748b; font-size:12px; }
    .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 15px; border:0; border-radius:9px; font-size:13px; font-weight:800; text-decoration:none; cursor:pointer; }
    .btn-light { background:#e2e8f0; color:#0f172a; }
    .btn-dark { background:#123b82; color:#fff; }
    .btn-red { color:#dc2626; background:transparent; padding:8px 0; }
    .btn:hover { filter:brightness(.96); }
    .back-button { margin-bottom:18px; }
    .create-panel { display:grid; grid-template-columns:1fr 1.4fr .75fr auto; gap:12px; align-items:end; margin-bottom:22px; padding:18px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 5px 16px rgba(15,23,42,.05); }
    label { display:block; margin-bottom:6px; color:#475569; font-size:11px; font-weight:800; }
    input { width:100%; padding:10px 11px; border:1px solid #dbe4f0; border-radius:8px; font-size:13px; }
    input:focus { outline:0; border-color:#2563eb; box-shadow:0 0 0 3px #dbeafe; }
    .panel { overflow:hidden; background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 5px 16px rgba(15,23,42,.04); }
    .panel-heading { display:flex; justify-content:space-between; gap:12px; padding:17px 18px; border-bottom:1px solid #eef2f7; }
    .panel-heading h2 { margin:0; color:#1e3a6f; font-size:15px; }
    .panel-heading span { color:#64748b; font-size:11px; }
    .table-wrap { overflow-x:auto; }
    table { width:100%; min-width:760px; border-collapse:collapse; }
    th { padding:12px 16px; background:#f8fafc; color:#475569; font-size:11px; text-align:left; }
    td { padding:13px 16px; border-top:1px solid #f1f5f9; color:#334155; font-size:12px; }
    .rack-code { color:#1e3a6f; font-weight:800; }
    .stock { color:#15803d; font-weight:800; }
    .actions { display:flex; align-items:center; gap:13px; white-space:nowrap; }
    .actions a { color:#2563eb; font-weight:800; text-decoration:none; }
    .empty { padding:36px 18px; color:#64748b; text-align:center; font-size:12px; }
    .alert { margin-bottom:18px; padding:12px 14px; border-radius:9px; font-size:13px; font-weight:700; }
    .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; }
    .alert-error { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }
    @media (max-width:760px) { .rack-heading { display:block; } .rack-heading .btn { margin-top:15px; } .create-panel { grid-template-columns:1fr 1fr; } }
    @media (max-width:500px) { .create-panel { grid-template-columns:1fr; } h1 { font-size:27px; } }
</style>

<div class="rack-page">
    <a class="btn btn-light back-button" href="{{ request('from') === 'dashboard' ? (auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff')) : route('denah-gudang') }}">&larr; Kembali</a>
    <div class="rack-heading">
        <div><div class="eyebrow">Administrasi lokasi inventaris</div><h1>Kelola Lokasi Rak</h1><p class="heading-copy">Atur area penyimpanan dan pantau isi setiap rak gudang.</p></div>
    </div>

    @if(session('sukses'))<div class="alert alert-success">{{ session('sukses') }}</div>@endif
    @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <form class="create-panel" method="POST" action="{{ route('rak.store') }}">
            @csrf
            <div><label for="kode_rak">Kode Rak</label><input id="kode_rak" type="text" name="kode_rak" placeholder="Contoh: R-01" required></div>
            <div><label for="nama_lokasi">Nama Lokasi</label><input id="nama_lokasi" type="text" name="nama_lokasi" placeholder="Contoh: Area ATK" required></div>
            <div><label for="kapasitas">Kapasitas (unit)</label><input id="kapasitas" type="number" name="kapasitas" min="0" placeholder="Opsional"></div>
            <button class="btn btn-dark" type="submit">+ Tambah Rak</button>
        </form>
    @endif

    <section class="panel">
        <div class="panel-heading"><h2>Lokasi Rak Gudang</h2><span>{{ $dataRak->count() }} lokasi terdaftar</span></div>
        @if($dataRak->isEmpty())
            <div class="empty">Belum ada lokasi rak yang terdaftar.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Kode Rak</th><th>Lokasi</th><th>Kapasitas</th><th>Jenis Barang</th><th>Total Stok</th><th>Aksi</th></tr></thead><tbody>
                @foreach($dataRak as $rak)
                    <tr><td class="rack-code">{{ $rak->kode_rak }}</td><td>{{ $rak->nama_lokasi }}</td><td>{{ $rak->kapasitas ? number_format($rak->kapasitas, 0, ',', '.') . ' unit' : 'Belum ditentukan' }}</td><td>{{ $rak->barang_count }} jenis</td><td class="stock">{{ number_format((int) ($rak->barang_sum_stok ?? 0), 0, ',', '.') }} unit</td><td><div class="actions"><a href="{{ route('rak.show', ['rak' => $rak, 'from' => 'kelola-rak', 'return_from' => request('from')]) }}">Lihat isi rak</a>@if(auth()->user()->isAdmin())<form method="POST" action="{{ route('rak.destroy', $rak) }}" onsubmit="return confirm('Hapus lokasi rak ini?')">@csrf @method('DELETE')<button class="btn btn-red" type="submit">Hapus</button></form>@endif</div></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>
</div>
@endsection
