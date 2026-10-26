<?php

namespace App\Notifications;

use App\Models\Barang;
use App\Models\StockOpnameBarang;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class OpnameSelisihWebPush extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Barang $barang, public StockOpnameBarang $opname) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title("Selisih opname: {$this->barang->nama}")
            ->body("Tercatat {$this->opname->jumlah_tercatat}, fisik {$this->opname->jumlah_fisik} — selisih {$this->opname->selisih}")
            ->tag("opname-selisih-{$this->opname->id}")
            ->icon('/favicon.ico')
            ->data(['url' => route('stock-opname-barang.riwayat', $this->barang->id)]);
    }

    /**
     * Kirim ke admin yang punya langganan push.
     * ponytail: dedup cache 24 jam per barang×user — longgarkan jika opname rangkap per barang wajib tiap selisih.
     */
    public static function kirim(Barang $barang, StockOpnameBarang $opname): void
    {
        $penerima = User::where('role', 'admin')
            ->whereHas('pushSubscriptions')
            ->get()
            ->filter(fn (User $user) => Cache::add(
                "webpush:opname-selisih:{$barang->id}:{$user->id}",
                1,
                now()->addDay(),
            ));

        NotificationFacade::send($penerima, new self($barang, $opname));
    }
}
