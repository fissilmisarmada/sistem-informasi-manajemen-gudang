@extends('layouts.app')

@section('content')
    <style>
        .book-detail-page { padding:24px 0; max-width:900px; margin:0 auto; }
        .book-detail-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:28px; box-shadow:0 8px 24px rgba(15,23,42,.06); display:grid; grid-template-columns:180px minmax(0,1fr); gap:28px; }
        .book-detail-cover { width:180px; height:240px; object-fit:cover; border-radius:12px; }
        .book-detail-placeholder { width:180px; height:240px; border-radius:12px; background:linear-gradient(135deg,#dbeafe,#bfdbfe); display:grid; place-items:center; color:#2563eb; font-weight:800; text-align:center; }
        .book-detail-title { margin:8px 0 18px; color:#123b82; font-size:32px; overflow-wrap:anywhere; }
        .book-detail-meta { display:grid; grid-template-columns:150px minmax(0,1fr); gap:12px; margin:0; color:#475569; font-size:14px; }
        .book-detail-meta dd { margin:0; overflow-wrap:anywhere; }
        .book-detail-actions { display:flex; flex-wrap:wrap; gap:10px; margin-top:24px; }
        .book-detail-actions a { padding:11px 16px; border-radius:9px; font-weight:700; }
        @media (max-width:600px) {
            .book-detail-page { padding:4px 0 24px; }
            .book-detail-card { display:block; padding:18px; border-radius:14px; }
            .book-detail-cover, .book-detail-placeholder { width:min(180px,100%); height:auto; aspect-ratio:3 / 4; margin:0 auto 20px; }
            .book-detail-title { font-size:25px; line-height:1.2; margin-bottom:16px; }
            .book-detail-meta { grid-template-columns:1fr; gap:4px; font-size:13px; }
            .book-detail-meta dt { margin-top:9px; color:#64748b; font-size:11px; font-weight:700; }
            .book-detail-meta dd { padding-bottom:5px; border-bottom:1px solid #f1f5f9; }
            .book-detail-actions { display:grid; grid-template-columns:1fr; margin-top:18px; }
            .book-detail-actions a { text-align:center; }
        }
    </style>
    <div class="book-detail-page">
        <a class="back-dashboard" href="{{ request('from') === 'cari' ? route('buku.cari', ['q' => request('q')]) : route('buku.index') }}">&larr; Kembali</a>
        <div class="book-detail-card">
            @if($buku->cover)
                <img class="book-detail-cover" src="{{ asset('storage/' . $buku->cover) }}" alt="Cover {{ $buku->judul }}">
            @else
                <div class="book-detail-placeholder">COVER<br>BUKU</div>
            @endif
            <div>
                <div style="font-size:12px;letter-spacing:1px;color:#64748b;text-transform:uppercase;font-weight:700;">Detail Buku</div>
                <h1 class="book-detail-title">{{ $buku->judul }}</h1>
                <dl class="book-detail-meta"><dt>Kode Buku</dt><dd style="font-weight:700;color:#1e3a6f;">{{ $buku->kode_buku }}</dd><dt>ISBN</dt><dd>{{ $buku->isbn ?? '-' }}</dd><dt>Kategori</dt><dd>{{ $buku->kategori ?? 'Umum' }}</dd><dt>Lokasi Rak</dt><dd>{{ $buku->rak?->kode_rak ?? 'Belum ditempatkan' }}</dd><dt>Jumlah Stok</dt><dd style="color:#15803d;font-weight:800;">{{ $buku->stok }} Buku</dd></dl>
                <div class="book-detail-actions">@if(auth()->user()->isAdmin() || auth()->user()->isStaff())<a href="{{ route('buku.edit-data', $buku) }}" style="background:#2563eb;color:#fff;">Edit Buku</a>@endif<a href="{{ request('from') === 'cari' ? route('buku.cari', ['q' => request('q')]) : route('buku.index') }}" style="background:#e2e8f0;color:#0f172a;">Kembali</a></div>
            </div>
        </div>
    </div>
@endsection