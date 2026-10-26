@extends('layouts.app')

@section('content')
<style>
    .rack-page{max-width:1120px;margin:0 auto}
    .page-head{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px}
    .page-head h1{margin:0;font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
    .eyebrow{color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .panel{overflow:hidden;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);position:relative;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .panel:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .panel::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .rack-info{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;padding:16px}
    .info-item{padding:13px 15px;background:#F8FAFC;border:1px solid var(--andon-line);border-radius:12px}
    .info-item span{display:block;color:var(--andon-muted);font-size:11px;font-weight:700}
    .info-item strong{display:block;margin-top:6px;color:var(--andon-ink);font-size:15px;letter-spacing:-.02em}
    .panel-heading{padding:16px 16px 14px;border-bottom:1px solid var(--andon-line);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
    .panel-heading h2{margin:0;color:var(--andon-ink);font-size:13px;font-weight:800;letter-spacing:-.02em}
    .table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
    table{width:100%;min-width:650px;border-collapse:collapse}
    th{padding:11px 14px;background:#F8FAFC;color:var(--andon-muted);font-size:11px;font-weight:800;letter-spacing:.06em;text-align:left;border-bottom:1px solid var(--andon-line)}
    td{padding:11px 14px;border-top:1px solid #F1F5F9;color:var(--andon-ink);font-size:12px}
    tr:last-child td{border-bottom:none}
    .empty{padding:40px 18px;color:var(--andon-muted);text-align:center;font-size:13px;font-weight:600}
    .stock{color:var(--andon-green);font-weight:800}
    .edit-toggle{display:inline-flex;align-items:center;gap:6px;min-height:44px;padding:0 16px;font-size:13px;font-weight:700;color:var(--andon-navy);background:#fff;border:1px solid var(--andon-line-strong);border-radius:10px;cursor:pointer;font-family:inherit}
    .edit-toggle:hover{border-color:var(--andon-ink);color:var(--andon-ink)}
    .edit-form{display:none;grid-template-columns:1fr 1fr 1fr;gap:12px;align-items:end;padding:16px;border-bottom:1px solid var(--andon-line);background:#F8FAFC}
    .edit-form.buka{display:grid}
    .edit-form label{display:block;margin-bottom:8px;color:var(--andon-muted);font-size:11px;font-weight:700;letter-spacing:.06em}
    .edit-form .btn,.edit-form .edit-toggle{min-height:44px}
    .edit-form__aksi{display:flex;gap:10px;align-items:center}
    .edit-form select.andon-control{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667085' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;background-size:16px;padding-right:36px}
    .andon-control{width:100%;padding:12px 14px;border:1px solid #E8EAF0;border-radius:14px;font-size:14px;color:var(--andon-ink);background:#FBFBFD;outline:none;box-sizing:border-box;font-family:inherit;transition:border-color .18s,box-shadow .18s,background .18s}
    .andon-control:focus{border-color:var(--andon-ink);box-shadow:0 0 0 3px rgba(15,23,42,.06);background:#fff}
    @media(max-width:600px){.rack-info{grid-template-columns:1fr} .edit-form{grid-template-columns:1fr} .page-head h1{font-size:20px}}
</style>

<div class="rack-page">
    <div class="page-head">
        @php
            $backUrl = match (request('from')) {
                'denah' => route('denah-gudang'),
                'kelola-rak' => route('rak.index', request('return_from') ? ['from' => request('return_from')] : []),
                default => route('denah-gudang'),
            };
        @endphp
        <a href="{{ $backUrl }}" class="back-link" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
        <div><div class="eyebrow">Lokasi inventaris gudang</div><h1>{{ $rak->kode_rak }}</h1></div>
    </div>

    <div class="panel">
        <div class="panel-heading"><h2>Informasi Rak</h2>@if(auth()->user()->isAdmin())<button type="button" class="edit-toggle" id="edit-rak-toggle" aria-expanded="false" aria-controls="edit-rak-form">Edit rak</button>@endif</div>
        <div class="rack-info">
            <div class="info-item"><span>Nama Lokasi</span><strong>{{ $rak->nama_lokasi }}</strong></div>
            <div class="info-item"><span>Total Jenis Barang</span><strong>{{ $barang->count() }} jenis</strong></div>
            <div class="info-item"><span>Kapasitas Rak</span><strong>{{ $rak->kapasitas ? number_format($rak->kapasitas, 0, ',', '.') . ' unit' : 'Belum ditentukan' }}</strong></div>
        </div>
        @if(auth()->user()->isAdmin())
            <form class="edit-form" id="edit-rak-form" method="POST" action="{{ route('rak.update', $rak) }}">
                @csrf @method('PUT')
                <div><label for="e-kode">Kode Rak</label><input class="andon-control" id="e-kode" type="text" name="kode_rak" value="{{ $rak->kode_rak }}" required autocomplete="off"></div>
                <div><label for="e-lokasi">Nama Lokasi</label><input class="andon-control" id="e-lokasi" type="text" name="nama_lokasi" value="{{ $rak->nama_lokasi }}" required autocomplete="off"></div>
                <div><label for="e-kapasitas">Kapasitas (unit)</label><input class="andon-control" id="e-kapasitas" type="number" name="kapasitas" min="0" value="{{ $rak->kapasitas }}" placeholder="Opsional"></div>
                <div><label for="e-area">Area Denah</label><select class="andon-control" id="e-area" name="denah_area_id"><option value="">— Tanpa area —</option>@foreach($denahAreas as $area)<option value="{{ $area->id }}" {{ (int) $rak->denah_area_id === $area->id ? 'selected' : '' }}>{{ $area->kode_area }} — {{ $area->nama }}</option>@endforeach</select></div>
                <div class="edit-form__aksi">
                    <button class="btn btn--primary" type="submit" style="padding:0 18px;font-size:13px">Simpan</button>
                    <button class="edit-toggle" type="button" id="edit-rak-batal">Batal</button>
                </div>
            </form>
        @endif
    </div>

    <section class="panel">
        <div class="panel-heading"><h2>Daftar Barang di Rak</h2></div>
        @if($barang->isEmpty())
            <div class="empty">Belum ada barang yang ditempatkan di rak ini.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Kode Barang</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th></tr></thead><tbody>
                @foreach($barang as $item)
                    <tr>
                        <td data-label="Kode Barang">{{ $item->kode_barang }}</td>
                        <td data-label="Nama Barang"><a href="{{ route('barang.show', $item) }}" style="color:var(--andon-navy);font-weight:700;">{{ $item->nama }}</a></td>
                        <td data-label="Kategori">{{ $item->kategori?->nama ?? '-' }}</td>
                        <td data-label="Stok" class="stock">{{ $item->stok }} {{ $item->satuan }}</td>
                    </tr>
                @endforeach
            </tbody></table></div>
        @endif</section>
</div>

@if(auth()->user()->isAdmin())
<script>
    (function () {
        var form = document.getElementById('edit-rak-form');
        var toggle = document.getElementById('edit-rak-toggle');
        var batal = document.getElementById('edit-rak-batal');
        if (!form || !toggle) return;
        function setBuka(buka) {
            form.classList.toggle('buka', buka);
            toggle.setAttribute('aria-expanded', String(buka));
            if (buka) { var i = form.querySelector('input[name="kapasitas"]'); if (i) i.focus(); }
        }
        toggle.addEventListener('click', function () { setBuka(!form.classList.contains('buka')); });
        if (batal) batal.addEventListener('click', function () { setBuka(false); });
    })();
</script>
@endif
@endsection
