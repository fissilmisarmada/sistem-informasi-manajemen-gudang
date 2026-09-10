@extends('layouts.app')

@section('content')
<style>
    .page-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
    .page-head h1{margin:0;font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
    .alert{padding:12px 14px;border-radius:12px;margin-bottom:16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px}
    .alert-success{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0}
    .alert-error{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA}
    .card{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);padding:0;overflow:hidden;position:relative;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .card:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .table-responsive{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}
    table{width:100%;min-width:620px;border-collapse:collapse}
    th{background:#F8FAFC;padding:11px 14px;text-align:left;font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--andon-muted);border-bottom:1px solid var(--andon-line);white-space:nowrap}
    td{padding:11px 14px;border-bottom:1px solid #F1F5F9;font-size:13px;color:var(--andon-ink)}
    tr:last-child td{border-bottom:none}
    tr:hover td{background:#F8FAFC}
    .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;border:1px solid transparent}
    .badge-blue{background:#EFF6FF;color:#1E3A5F;border-color:#DBEAFE}
    .modal-overlay{display:none;position:fixed;inset:0;padding:12px;background:rgba(15,23,42,.45);z-index:100;align-items:center;justify-content:center}
    .modal-overlay.active{display:flex}
    .modal{background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;padding:28px;width:100%;max-width:480px;max-height:calc(100dvh - 24px);overflow-y:auto;box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05);position:relative;overflow:hidden}
    .modal::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-amber)}
    .modal h3{margin:0 0 16px;font-size:17px;font-weight:800;letter-spacing:-.02em;color:var(--andon-ink)}
    .form-group{margin-bottom:14px}
    label{display:block;font-size:11px;font-weight:700;color:var(--andon-muted);margin-bottom:8px;letter-spacing:.06em}
    .andon-control{width:100%;padding:12px 14px;border:1px solid #E8EAF0;border-radius:14px;font-size:14px;color:var(--andon-ink);background:#FBFBFD;outline:none;transition:border-color .18s,box-shadow .18s,background .18s}
    .andon-control:focus{border-color:var(--andon-ink);box-shadow:0 0 0 3px rgba(15,23,42,.06);background:#fff}
    textarea.andon-control{resize:vertical;min-height:72px}
    .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:18px}
    @media(max-width:640px){.page-head h1{font-size:20px}}
</style>

<a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn--ghost" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">← Kembali</a>

<div class="page-head">
    <h1>Kategori Barang</h1>
    @if(auth()->user()->isAdmin())
        <button class="btn btn--primary" onclick="document.getElementById('modal-tambah').classList.add('active')">+ Tambah Kategori</button>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah Barang</th>
                @if(auth()->user()->isAdmin())
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($kategoris as $kategori)
            <tr>
                <td><span class="badge badge-blue">{{ $kategori->kode_kategori }}</span></td>
                <td><strong>{{ $kategori->nama }}</strong></td>
                <td style="color:var(--andon-muted)">{{ $kategori->deskripsi ?? '-' }}</td>
                <td>{{ $kategori->barang_count }} barang</td>
                @if(auth()->user()->isAdmin())
                <td>
                    <button class="btn btn--ghost edit-btn" style="min-height:32px;padding:0 10px;font-size:12px;"
                        data-id="{{ $kategori->id }}"
                        data-kode="{{ $kategori->kode_kategori }}"
                        data-nama="{{ $kategori->nama }}"
                        data-deskripsi="{{ $kategori->deskripsi ?? '' }}">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('kategori.destroy', $kategori) }}" style="display:inline"
                        onsubmit="return confirm('Hapus kategori {{ $kategori->nama }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--ghost" style="min-height:32px;padding:0 10px;font-size:12px;color:var(--andon-red);border-color:#FECACA">Hapus</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:var(--andon-faint);padding:32px;font-weight:600">Belum ada kategori.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@if(auth()->user()->isAdmin())
{{-- Modal Tambah --}}
<div class="modal-overlay" id="modal-tambah">
    <div class="modal">
        <h3>Tambah Kategori</h3>
        <form method="POST" action="{{ route('kategori.store') }}">
            @csrf
            <div class="form-group">
                <label>Kode Kategori</label>
                <input class="andon-control" type="text" name="kode_kategori" placeholder="Contoh: ATK, BKU, KMP" required maxlength="20">
            </div>
            <div class="form-group">
                <label>Nama Kategori</label>
                <input class="andon-control" type="text" name="nama" placeholder="Contoh: Alat Tulis Kantor" required maxlength="100">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="andon-control" name="deskripsi" rows="3" placeholder="Opsional"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn--ghost" onclick="document.getElementById('modal-tambah').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <h3>Edit Kategori</h3>
        <form method="POST" id="form-edit" action="">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Kode Kategori</label>
                <input class="andon-control" type="text" name="kode_kategori" id="edit-kode" required maxlength="20">
            </div>
            <div class="form-group">
                <label>Nama Kategori</label>
                <input class="andon-control" type="text" name="nama" id="edit-nama" required maxlength="100">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="andon-control" name="deskripsi" id="edit-deskripsi" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn--ghost" onclick="document.getElementById('modal-edit').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn--primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, kode, nama, deskripsi) {
    document.getElementById('form-edit').action = '/kategori/' + id;
    document.getElementById('edit-kode').value = kode;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-deskripsi').value = deskripsi;
    document.getElementById('modal-edit').classList.add('active');
}
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        openEditModal(this.dataset.id, this.dataset.kode, this.dataset.nama, this.dataset.deskripsi);
    });
});
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
});
</script>
@endif
@endsection
