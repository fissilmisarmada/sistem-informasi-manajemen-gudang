<?php

namespace App\Notifications;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class StokMenipisWebPush extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Barang $barang) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        $rak = $this->barang->rak;
        $lokasi = $rak ? " — Rak {$rak->kode_rak} ({$rak->nama_lokasi})" : '';

        return (new WebPushMessage)
            ->title("Stok menipis: {$this->barang->nama}")
            ->body("Sisa {$this->barang->stok} {$this->barang->satuan} (min. {$this->barang->stok_minimum}){$lokasi}")
            ->tag("stok-menipis-{$this->barang->id}")
            ->icon('/favicon.ico')
            ->data(['url' => route('barang.show', $this->barang->id)]);
    }

    /**
     * Kirim ke admin + pimpinan yang punya langganan push.
     * ponytail: dedup cache 24 jam per barang×user — naikkan jadi per-event kalau butuh hitung ulang waktu.
     */
    public static function kirim(Barang $barang): void
    {
        $penerima = User::whereIn('role', ['admin', 'pimpinan'])
            ->whereHas('pushSubscriptions')
            ->get()
            ->filter(fn (User $user) => Cache::add(
                "webpush:stok-menipis:{$barang->id}:{$user->id}",
                1,
                now()->addDay(),
            ));

        NotificationFacade::send($penerima, new self($barang));
    }
}
