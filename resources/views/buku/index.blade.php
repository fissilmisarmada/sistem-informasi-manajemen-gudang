@extends('layouts.app')

@section('content')
    <style>
        .book-page { padding: 18px 0 32px; }
        .book-toolbar { display:flex; align-items:end; justify-content:space-between; gap:18px; margin-bottom:20px; }
        .book-toolbar h1 { margin:6px 0 0; color:#123b82; font-size:32px; }
        .book-search { display:flex; gap:10px; align-items:center; background:#fff; border:1px solid #dbe4f0; border-radius:12px; padding:10px 14px; margin-bottom:18px; }
        .book-search input { width:100%; border:0; outline:0; font-size:14px; background:transparent; }
        .book-list { display:grid; gap:11px; }
        .book-card { display:grid; grid-template-columns:58px minmax(0,1fr) auto; align-items:center; gap:14px; padding:13px; background:#fff; border:1px solid #e5eaf1; border-radius:14px; box-shadow:0 4px 13px rgba(15,23,42,.05); cursor:pointer; transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .book-card:hover { transform:translateY(-3px); border-color:#93c5fd; box-shadow:0 12px 24px rgba(37,99,235,.12); }
        .book-card:focus-visible { outline:3px solid #93c5fd; outline-offset:2px; }
        .book-cover { width:58px; height:76px; border-radius:7px; object-fit:cover; background:linear-gradient(135deg,#dbeafe,#bfdbfe); display:grid; place-items:center; color:#2563eb; font-size:11px; font-weight:800; text-align:center; }
        .book-title { margin:0 0 4px; color:#1e3a6f; font-size:14px; font-weight:800; }
        .book-meta { color:#64748b; font-size:11px; line-height:1.6; }
        .book-tags { display:flex; flex-wrap:wrap; gap:5px; margin-top:6px; }
        .book-tag { padding:3px 7px; border-radius:5px; background:#eff6ff; color:#2563eb; font-size:10px; font-weight:700; }
        .book-stock { background:#dcfce7; color:#15803d; }
        .book-actions { display:flex; flex-direction:column; gap:7px; }
        .book-action { width:34px; height:34px; display:grid; place-items:center; border:1px solid #dbe4f0; border-radius:9px; background:#fff; color:#2563eb; font-weight:900; }
        @media (max-width:650px) { .book-toolbar { align-items:stretch; flex-direction:column; } .book-toolbar h1 { font-size:27px; } .book-card { grid-template-columns:52px minmax(0,1fr) auto; gap:10px; } .book-cover { width:52px; height:70px; } }
    </style>
    <div class="book-page">
        <a class="back-dashboard" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}">&larr; Kembali ke Dashboard</a>
        <div class="book-toolbar">
            <div>
                <div style="font-size:12px; letter-spacing:1px; color:#64748b; text-transform:uppercase; font-weight:700;">Katalog Gudang</div>
                <h1>Data Buku</h1>
                <div style="color:#64748b;font-size:13px;">Kelola seluruh data buku yang tersedia di gudang.</div>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                <a href="{{ route('buku.create-data') }}" style="padding:12px 18px; background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff; border-radius:10px; text-decoration:none; font-weight:700;">+ Tambah Buku</a>
            @endif
        </div>

        @if(session('sukses'))
            <div style="padding:12px 14px; background:#dcfce7; border:1px solid #86efac; color:#166534; border-radius:10px; margin-bottom:16px; font-weight:600;">
                {{ session('sukses') }}
            </div>
        @endif

        <form class="book-search" method="GET" action="{{ route('buku.index') }}"><span style="color:#94a3b8;font-size:20px;">⌕</span><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul, kode buku, atau ISBN..."><button type="submit" style="border:0;background:#eff6ff;color:#2563eb;border-radius:8px;padding:8px 12px;font-weight:800;cursor:pointer;">Cari</button></form>

        <div style="display:flex;justify-content:space-between;align-items:center;margin:8px 0 12px;color:#64748b;font-size:13px;"><span>Total <strong style="color:#1e3a6f;">{{ $bukus->total() }}</strong> buku</span><span>Urutkan: <strong style="color:#1e3a6f;">Terbaru</strong></span></div>

        @if($bukus->isEmpty())
            <div style="padding:24px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;color:#64748b;">Tidak ada data buku.</div>
        @else
            <div class="book-list">
                @foreach($bukus as $buku)
                    <article class="book-card" data-detail-url="{{ route('buku.show', $buku) }}" tabindex="0" role="link" aria-label="Lihat detail {{ $buku->judul }}">
                        @if($buku->cover)
                            <img class="book-cover" src="{{ asset('storage/' . $buku->cover) }}" alt="Cover {{ $buku->judul }}">
                        @else
                            <div class="book-cover">COVER<br>BUKU</div>
                        @endif
                        <div>
                            <h2 class="book-title">{{ $buku->judul }}</h2>
                            <div class="book-meta">Kode: {{ $buku->kode_buku }}<br>ISBN: {{ $buku->isbn ?? '-' }}</div>
                            <div class="book-tags"><span class="book-tag">{{ $buku->kategori ?? 'Umum' }}</span><span class="book-tag" style="background:#f8fafc;color:#64748b;">{{ $buku->rak?->kode_rak ?? 'Belum ada rak' }}</span><span class="book-tag book-stock">{{ $buku->stok }} Buku</span></div>
                        </div>
                        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <div class="book-actions"><a class="book-action" href="{{ route('buku.edit-data', $buku) }}" title="Edit buku" onclick="event.stopPropagation()">&#9998;</a></div>
                        @endif
                    </article>
                @endforeach
            </div>
            <div style="margin-top:16px;">{{ $bukus->links() }}</div>
        @endif
    </div>
    <script>
        document.querySelectorAll('[data-detail-url]').forEach(function (card) {
            card.addEventListener('click', function (event) {
                if (!event.target.closest('a, button, form')) {
                    window.location.href = card.dataset.detailUrl;
                }
            });
            card.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    window.location.href = card.dataset.detailUrl;
                }
            });
        });
    </script>
@endsection
