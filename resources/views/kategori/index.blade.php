@extends('layouts.app')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-primary:hover { background:#2563eb; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-danger:hover { background:#dc2626; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .btn-secondary:hover { background:#cbd5e1; }
    .alert { padding:12px 16px; border-radius:9px; margin-bottom:18px; font-weight:600; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
    .card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.08); padding:24px; margin-bottom:24px; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:12px 14px; text-align:left; font-size:13px; font-weight:700; color:#475569; }
    td { padding:12px 14px; border-bottom:1px solid #f1f5f9; font-size:14px; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f8fafc; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-blue { background:#dbeafe; color:#1d4ed8; }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:100; align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal { background:#fff; border-radius:14px; padding:28px; width:100%; max-width:480px; }
    .modal h3 { margin:0 0 18px; font-size:18px; font-weight:800; }
    .form-group { margin-bottom:16px; }
    label { display:block; font-size:13px; font-weight:700; color:#475569; margin-bottom:6px; }
    input[type=text], textarea { width:100%; padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; }
    input:focus, textarea:focus { outline:none; border-color:#3b82f6; }
    .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
</style>

<a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn-secondary" style="margin-bottom:16px; padding:8px 14px; font-size:13px;">← Kembali</a>

<div class="page-header">
    <h1 class="page-title">Kategori Barang</h1>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary" onclick="document.getElementById('modal-tambah').classList.add('active')">+ Tambah Kategori</button>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card">
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
                <td>{{ $kategori->deskripsi ?? '-' }}</td>
                <td>{{ $kategori->barang_count }} barang</td>
                @if(auth()->user()->isAdmin())
                <td>
                    <button class="btn btn-secondary edit-btn" style="padding:6px 12px;font-size:12px;"
                        data-id="{{ $kategori->id }}"
                        data-kode="{{ $kategori->kode_kategori }}"
                        data-nama="{{ $kategori->nama }}"
                        data-deskripsi="{{ $kategori->deskripsi ?? '' }}">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('kategori.destroy', $kategori) }}" style="display:inline"
                        onsubmit="return confirm('Hapus kategori {{ $kategori->nama }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding:6px 12px;font-size:12px;">Hapus</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:32px;">Belum ada kategori.</td></tr>
            @endforelse
        </tbody>
    </table>
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
                <input type="text" name="kode_kategori" placeholder="Contoh: ATK, BKU, KMP" required maxlength="20">
            </div>
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama" placeholder="Contoh: Alat Tulis Kantor" required maxlength="100">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Opsional"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-tambah').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
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
                <input type="text" name="kode_kategori" id="edit-kode" required maxlength="20">
            </div>
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama" id="edit-nama" required maxlength="100">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" id="edit-deskripsi" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-edit').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
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
