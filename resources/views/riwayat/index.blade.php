@extends('layouts.app')

@section('content')
    <div style="padding:24px 0;">
    <a class="back-dashboard" href="{{ request('from') === 'cari' ? route('buku.cari', ['q' => request('q')]) : (auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan'))) }}">&larr; {{ request('from') === 'cari' ? 'Kembali ke Cari Buku' : 'Kembali ke Dashboard' }}</a>
    <div style="margin-bottom:22px;">
        <div style="font-size:12px;letter-spacing:1px;color:#64748b;text-transform:uppercase;font-weight:700;">Kontrol Inventaris</div>
        <h1 style="margin:8px 0 0;font-size:32px;color:#0f172a;">Riwayat Penempatan Buku</h1>
    </div>
    
    @if($riwayat->isEmpty())
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;color:#64748b;">Belum ada riwayat penempatan.</div>
    @else
        <div style="overflow-x:auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;"><table cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;min-width:680px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:14px;color:#475569;">Buku</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Rak</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Staff</th>
                    <th style="text-align:left;padding:14px;color:#475569;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $item)
                    <tr>
                        <td style="padding:14px;font-weight:700;">{{ $item->buku->judul ?? '-' }}</td>
                        <td style="padding:14px;">{{ $item->rak->kode_rak ?? '-' }}</td>
                        <td style="padding:14px;">{{ $item->staff->name ?? '-' }}</td>
                        <td style="padding:14px;color:#64748b;">{{ $item->tanggal ? $item->tanggal->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    @endif
    </div>
@endsection
