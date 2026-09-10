@extends('layouts.app')

@section('content')
<style>
    .rack-page{max-width:1120px;margin:0 auto}
    .page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
    .page-head h1{margin:0;font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
    .page-head p{margin:6px 0 0;font-size:12px;color:var(--andon-muted);line-height:1.4}
    .eyebrow{color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .alert{padding:12px 14px;border-radius:12px;margin-bottom:16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px}
    .alert-success{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0}
    .alert-error{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA}
    .create-panel{display:grid;grid-template-columns:1fr 1.4fr .75fr auto;gap:12px;align-items:end;margin-bottom:18px;padding:20px;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);position:relative;overflow:hidden;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .create-panel:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .create-panel::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    label{display:block;margin-bottom:8px;color:var(--andon-muted);font-size:11px;font-weight:700;letter-spacing:.06em}
    .andon-control{width:100%;padding:12px 14px;border:1px solid #E8EAF0;border-radius:14px;font-size:14px;color:var(--andon-ink);background:#FBFBFD;outline:none;transition:border-color .18s,box-shadow .18s,background .18s}
    .andon-control:focus{border-color:var(--andon-ink);box-shadow:0 0 0 3px rgba(15,23,42,.06);background:#fff}
    .panel{overflow:hidden;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);position:relative;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .panel:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .panel::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .panel-heading{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 16px 14px;border-bottom:1px solid var(--andon-line)}
    .panel-heading h2{margin:0;color:var(--andon-ink);font-size:13px;font-weight:800;letter-spacing:-.02em}
    .panel-heading span{color:var(--andon-faint);font-size:11px;font-weight:700}
    .table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
    table{width:100%;min-width:760px;border-collapse:collapse}
    th{padding:11px 14px;background:#F8FAFC;color:var(--andon-muted);font-size:11px;font-weight:800;letter-spacing:.06em;text-align:left;border-bottom:1px solid var(--andon-line);white-space:nowrap}
    td{padding:12px 14px;border-top:1px solid #F1F5F9;color:var(--andon-ink);font-size:12px}
    tr:last-child td{border-bottom:none}
    .rack-code{color:var(--andon-ink);font-weight:800}
    .stock{color:var(--andon-green);font-weight:800}
    .actions{display:flex;align-items:center;gap:10px;white-space:nowrap}
    .actions a{color:var(--andon-navy);font-weight:700;text-decoration:none;font-size:12px}
    .actions a:hover{color:var(--andon-ink);text-decoration:underline}
    .empty{padding:40px 18px;color:var(--andon-muted);text-align:center;font-size:13px;font-weight:600}
    @media(max-width:760px){.page-head{display:block} .create-panel{grid-template-columns:1fr 1fr} .create-panel .btn{grid-column:1/-1}}
    @media(max-width:500px){.create-panel{grid-template-columns:1fr} .page-head h1{font-size:20px}}
</style>

<div class="rack-page">
    <a class="btn btn--ghost" href="{{ request('from') === 'dashboard' ? (auth()->user()->isAdmin() ? route('dashboard.admin') : route('dashboard.staff')) : route('denah-gudang') }}" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">&larr; Kembali</a>
    <div class="page-head">
        <div><div class="eyebrow">Administrasi lokasi inventaris</div><h1>Kelola Lokasi Rak</h1><p>Atur area penyimpanan dan pantau isi setiap rak gudang.</p></div>
    </div>

    @if(session('sukses'))<div class="alert alert-success">{{ session('sukses') }}</div>@endif
    @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <form class="create-panel" method="POST" action="{{ route('rak.store') }}">
            @csrf
            <div><label for="kode_rak">Kode Rak</label><input class="andon-control" id="kode_rak" type="text" name="kode_rak" placeholder="Contoh: R-01" required></div>
            <div><label for="nama_lokasi">Nama Lokasi</label><input class="andon-control" id="nama_lokasi" type="text" name="nama_lokasi" placeholder="Contoh: Area ATK" required></div>
            <div><label for="kapasitas">Kapasitas (unit)</label><input class="andon-control" id="kapasitas" type="number" name="kapasitas" min="0" placeholder="Opsional"></div>
            <button class="btn btn--primary" type="submit">+ Tambah Rak</button>
        </form>
    @endif

    <section class="panel">
        <div class="panel-heading"><h2>Lokasi Rak Gudang</h2><span>{{ $dataRak->count() }} lokasi terdaftar</span></div>
        @if($dataRak->isEmpty())
            <div class="empty">Belum ada lokasi rak yang terdaftar.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Kode Rak</th><th>Lokasi</th><th>Kapasitas</th><th>Jenis Barang</th><th>Total Stok</th><th>Aksi</th></tr></thead><tbody>
                @foreach($dataRak as $rak)
                    <tr><td class="rack-code">{{ $rak->kode_rak }}</td><td>{{ $rak->nama_lokasi }}</td><td>{{ $rak->kapasitas ? number_format($rak->kapasitas, 0, ',', '.') . ' unit' : 'Belum ditentukan' }}</td><td>{{ $rak->barang_count }} jenis</td><td class="stock">{{ number_format((int) ($rak->barang_sum_stok ?? 0), 0, ',', '.') }} unit</td><td><div class="actions"><a href="{{ route('rak.show', ['rak' => $rak, 'from' => 'kelola-rak', 'return_from' => request('from')]) }}">Lihat isi rak</a>@if(auth()->user()->isAdmin())<form method="POST" action="{{ route('rak.destroy', $rak) }}" onsubmit="return confirm('Hapus lokasi rak ini?')">@csrf @method('DELETE')<button class="btn btn--ghost" type="submit" style="min-height:32px;padding:0 10px;font-size:12px;color:var(--andon-red);border-color:#FECACA">Hapus</button></form>@endif</div></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>
</div>
@endsection
