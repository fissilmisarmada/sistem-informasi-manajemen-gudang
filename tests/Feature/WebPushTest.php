<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use App\Notifications\OpnameSelisihWebPush;
use App\Notifications\StokMenipisWebPush;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WebPushTest extends TestCase
{
    use RefreshDatabase;

    private function subscriber(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        $user->updatePushSubscription("https://push.test/{$role}/{$user->id}", 'p256dh-key', 'auth-key');

        return $user;
    }

    private function buatBarang(int $stok, int $stokMinimum): Barang
    {
        $kategori = Kategori::create(['kode_kategori' => 'TST', 'nama' => 'Test']);

        return Barang::create([
            'kode_barang' => 'BRG-'.uniqid(),
            'nama' => 'Bolt M8',
            'kategori_id' => $kategori->id,
            'satuan' => 'pcs',
            'stok' => $stok,
            'stok_minimum' => $stokMinimum,
        ]);
    }

    public function test_subscribe_dan_unsubscribe_endpoints(): void
    {
        $user = $this->subscriber('admin');

        $this->actingAs($user)->postJson(route('notifikasi.subscribe'), [
            'endpoint' => 'https://push.test/sub-1',
            'keys' => ['p256dh' => 'k1', 'auth' => 'a1'],
        ])->assertNoContent();
        $this->assertDatabaseHas('push_subscriptions', ['endpoint' => 'https://push.test/sub-1']);

        $this->actingAs($user)->deleteJson(route('notifikasi.unsubscribe'), [
            'endpoint' => 'https://push.test/sub-1',
        ])->assertNoContent();
        $this->assertDatabaseMissing('push_subscriptions', ['endpoint' => 'https://push.test/sub-1']);
    }

    public function test_staff_dilarang_subscribe(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->postJson(route('notifikasi.subscribe'), [
            'endpoint' => 'https://push.test/forbidden',
            'keys' => ['p256dh' => 'k1', 'auth' => 'a1'],
        ])->assertForbidden();
    }

    public function test_mutasi_keluar_menyeberang_menipis_notifikasi_admin_dan_pimpinan(): void
    {
        Notification::fake();
        $admin = $this->subscriber('admin');
        $pimpinan = $this->subscriber('pimpinan');
        $staff = User::factory()->create(['role' => 'staff']);
        $barang = $this->buatBarang(stok: 12, stokMinimum: 10);

        $this->actingAs($staff)->post(route('mutasi-barang.store'), [
            'barang_id' => $barang->id,
            'jenis' => 'keluar',
            'jumlah' => 3, // 12 → 9, menyeberang ke menipis
        ])->assertRedirect();

        Notification::assertSentTo([$admin, $pimpinan], StokMenipisWebPush::class);
        Notification::assertNotSentTo($staff, StokMenipisWebPush::class);
    }

    public function test_tidak_notifikasi_jika_tidak_menyeberang_menipis(): void
    {
        Notification::fake();
        $admin = $this->subscriber('admin');
        $barang = $this->buatBarang(stok: 12, stokMinimum: 10);

        $this->actingAs($admin)->post(route('mutasi-barang.store'), [
            'barang_id' => $barang->id,
            'jenis' => 'keluar',
            'jumlah' => 1, // 12 → 11, masih di atas minimum
        ])->assertRedirect();

        Notification::assertNotSentTo($admin, StokMenipisWebPush::class);
    }

    public function test_notifikasi_stok_tidak_berulang_dalam_24_jam(): void
    {
        Notification::fake();
        $admin = $this->subscriber('admin');
        $barang = $this->buatBarang(stok: 12, stokMinimum: 10);
        $mutasi = ['barang_id' => $barang->id, 'jenis' => 'keluar', 'jumlah' => 3];

        $this->actingAs($admin)->post(route('mutasi-barang.store'), $mutasi)->assertRedirect(); // 9 → kirim
        $this->actingAs($admin)->post(route('mutasi-barang.store'), [
            'barang_id' => $barang->id, 'jenis' => 'masuk', 'jumlah' => 4,
        ])->assertRedirect(); // 13
        $this->actingAs($admin)->post(route('mutasi-barang.store'), $mutasi)->assertRedirect(); // 10 → menipis lagi, cooldown

        Notification::assertSentToTimes($admin, StokMenipisWebPush::class, 1);
    }

    public function test_opname_selisih_notifikasi_admin_saja(): void
    {
        Notification::fake();
        $admin = $this->subscriber('admin');
        $pimpinan = $this->subscriber('pimpinan');
        $barang = $this->buatBarang(stok: 10, stokMinimum: 0);

        $this->actingAs($admin)->post(route('stock-opname-barang.store', $barang->id), [
            'jumlah_fisik' => 7, // selisih 3
        ])->assertRedirect();

        Notification::assertSentTo($admin, OpnameSelisihWebPush::class);
        Notification::assertNotSentTo($pimpinan, OpnameSelisihWebPush::class);
    }

    public function test_opname_tanpa_selisih_tidak_notifikasi(): void
    {
        Notification::fake();
        $admin = $this->subscriber('admin');
        $barang = $this->buatBarang(stok: 10, stokMinimum: 0);

        $this->actingAs($admin)->post(route('stock-opname-barang.store', $barang->id), [
            'jumlah_fisik' => 10,
        ])->assertRedirect();

        Notification::assertNotSentTo($admin, OpnameSelisihWebPush::class);
    }
}
