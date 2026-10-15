# Sistem Informasi Manajemen Gudang

Aplikasi web untuk mengelola barang gudang secara terstruktur: mulai dari input dan pencarian barang (scan barcode atau manual), penempatan ke rak dan denah area visual, pencatatan mutasi masuk/keluar, stock opname, sampai laporan.

Berbeda dari inventaris generik, sistem ini memetakan setiap barang ke **lokasi fisik** (rak atau area pada denah gudang) yang bisa dilihat di peta dan dijangkau lewat **scan barcode** dari HP di lapangan.

## Fitur

- **Autentikasi berbasis role**: login, lupa/reset password, dan dashboard terpisah untuk Admin, Staff, dan Pimpinan.
- **Master data**: barang (dengan foto, satuan, stok, dan stok minimum), kategori, rak, dan denah area.
- **Denah gudang visual**: lihat rak beserta status isinya, tempatkan barang ke rak, serta kelola area pada denah (posisi, ukuran, warna, gambar denah per area) dan pindahkan barang ke area. Posisi area bisa digeser (drag & drop via `PATCH /denah-area/{id}/posisi`), upload/hapus gambar denah, dan assign barang ke area (otomatis lepas dari rak).
- **Input & pencarian barang**:
  - Scan barcode lewat kamera HP (`/input-barang`): lookup via `GET /input-barang/lookup` → jika kode dikenali `POST /input-barang/proses` stok otomatis bertambah dan barang dipindah ke rak yang dipilih; jika belum dikenali `POST /input-barang/manual` lengkapi data barang lewat form manual.
  - Pencarian global (`/cari`) berdasarkan nama atau kode, dengan filter tipe (semua/buku/barang) dan kategori.
  - Input suara: rekam audio → `POST /voice/transcribe` (Groq `whisper-large-v3` prioritas, fallback OpenAI `whisper-1`, bahasa `id`) → isi otomatis ke form.
- **Mutasi barang**: catat barang masuk dan keluar. Barang keluar ditolak jika stok tidak mencukupi.
- **Stock opname**: bandingkan jumlah tercatat dengan jumlah fisik per barang, lengkap dengan riwayatnya. Opname hanya mencatat selisih (tercatat dikurangi fisik) dan tidak mengubah stok barang secara otomatis; koreksi stok dilakukan lewat mutasi.
- **Peringatan stok menipis**: barang dengan stok di bawah atau sama dengan stok minimum ditampilkan di dashboard dan halaman khusus.
- **Laporan**: ringkasan stok dan aktivitas terbaru, ekspor data barang ke CSV, dan impor barang baru dari CSV.
- **Manajemen user** (khusus Admin): tambah dan hapus user beserta rolenya.

## Hak Akses per Role

| Fitur | Admin | Staff | Pimpinan |
| --- | :---: | :---: | :---: |
| Dashboard sesuai role | ✅ | ✅ | ✅ |
| Lihat barang, kategori, rak, denah gudang, pencarian, mutasi, laporan | ✅ | ✅ | ✅ |
| Tambah barang baru | ✅ | ✅ | ❌ |
| Ubah/hapus barang | ✅ | ❌ | ❌ |
| Tambah/ubah/hapus kategori dan rak | ✅ | ❌ | ❌ |
| Kelola denah area (tambah/ubah/hapus area, upload gambar denah, geser posisi) dan tempatkan barang | ✅ | ✅ | ❌ |
| Input barang (scan barcode / manual / suara) | ✅ | ✅ | ❌ |
| Catat mutasi barang | ✅ | ✅ | ❌ |
| Stock opname | ✅ | ✅ | ❌ |
| Ekspor laporan CSV | ✅ | ✅ | ✅ |
| Impor laporan CSV | ✅ | ❌ | ❌ |
| Kelola user | ✅ | ❌ | ❌ |

Pembatasan akses dilakukan lewat middleware `role:*` (`app/Http/Middleware/CheckRole.php`) + `SecurityHeaders`; login dibatasi `throttle:10,1` dan reset password `throttle:5,1`.

## Teknologi

- **Backend**: PHP ^8.2, Laravel 12
- **Frontend**: Blade, Vite 7, Tailwind CSS 4
- **Database**: SQLite (default di `.env.example`); MySQL bisa dipakai dengan mengubah konfigurasi `DB_*`
- **Scanner barcode**: `BarcodeDetector` bawaan browser, dengan fallback `html5-qrcode` (dimuat dari CDN)
- **Input suara**: Whisper via Groq (`whisper-large-v3`, prioritas) atau OpenAI (`whisper-1`) di `POST /voice/transcribe` (`VoiceController`)
- **Testing**: PHPUnit dan Playwright (E2E)

## Struktur Data

| Tabel | Isi |
| --- | --- |
| `users` | Akun pengguna dengan kolom `role` (`admin`, `staff`, `pimpinan`) |
| `kategori` | Kategori barang (`kode_kategori`, `nama`, `deskripsi`) — helper `Kategori::bukuId()` untuk filter BKU |
| `rak` | Rak penyimpanan (`kode_rak`, `nama_lokasi`, `kapasitas`, `denah_area_id` opsional ke area) |
| `denah_area` | Area pada denah visual (`kode_area`, posisi `x`/`y`, ukuran `w`/`h`, `warna`, `denah_image`) |
| `barang` | Data barang (`kode_barang`, `nama`, `satuan`, `stok`, `stok_minimum` otomatis via `stokMinimumForKategori` — FRN=0 lainnya 10, `rak_id`/`denah_area_id` saling eksklusif, `gambar`, `keterangan` JSON) |
| `mutasi_barang` | Riwayat barang masuk/keluar beserta staff pencatatnya |
| `stock_opname_barang` | Riwayat opname: jumlah tercatat, jumlah fisik, dan selisih |
| `audit_logs` | Log audit perubahan data |

Data buku dari versi sebelumnya sudah dimigrasikan menjadi `barang` dengan kategori `BKU`.

## Instalasi

### Prasyarat

- PHP 8.2 atau lebih baru (ekstensi umum Laravel, termasuk `pdo_sqlite` bila memakai SQLite)
- Composer
- Node.js 20 atau lebih baru dan npm

### Langkah-langkah

1. Clone repositori dan masuk ke foldernya.

2. Jalankan setup otomatis (install dependency, salin `.env`, buat app key, migrasi, dan build asset):

   ```bash
   composer setup
   ```

3. Sesuaikan `.env` untuk pengembangan lokal. File `.env.example` berisi default untuk production, jadi ubah nilai berikut:

   ```env
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000
   SESSION_SECURE_COOKIE=false
   # Opsional untuk input suara (pilih salah satu):
   GROQ_API_KEY=gsk_xxxx
   # OPENAI_API_KEY=sk-xxxx
   ```

   Nilai `APP_ENV=local` juga diperlukan agar seeder user bisa berjalan. Untuk voice, isi `GROQ_API_KEY` (prioritas, `whisper-large-v3`) atau `OPENAI_API_KEY` lalu `php artisan config:clear`.

4. Buat symlink agar foto barang yang diunggah bisa diakses:

   ```bash
   php artisan storage:link
   ```

5. Isi data awal (user demo, rak, kategori, barang contoh, mutasi, dan opname):

   ```bash
   php artisan db:seed
   ```

6. Jalankan aplikasi beserta queue, log, dan Vite:

   ```bash
   composer dev
   ```

   Aplikasi tersedia di `http://127.0.0.1:8000`.

### Akun demo (hanya untuk pengembangan)

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@gmail.com` | `admin123` |
| Staff | `staff@gmail.com` | `staff123` |
| Pimpinan | `pimpinan@gmail.com` | `pimpinan123` |

`UserSeeder` otomatis menolak berjalan saat `APP_ENV=production`. Buat akun asli lewat menu **Users** (login sebagai Admin) dan jangan pakai akun demo di server production.

## Menggunakan Scan Barcode

- Kamera hanya bisa diakses lewat **HTTPS** atau `localhost`. Untuk mencoba dari HP saat pengembangan, buka aplikasi lewat tunnel HTTPS (misalnya ngrok) atau domain ber-HTTPS.
- Format yang didukung: EAN-13, EAN-8, Code 128, Code 39, QR Code, UPC-A, dan UPC-E.
- Jika browser tidak mendukung `BarcodeDetector`, scanner memakai `html5-qrcode` yang dimuat dari CDN, sehingga butuh koneksi internet.
- Kode sumber scanner ada di `resources/js/barcode-scanner.js`.

## Ekspor dan Impor CSV

Ekspor laporan menghasilkan CSV dengan kolom berikut:

```
kode_barang, nama, kode_kategori, kategori, satuan, stok, stok_minimum, kode_rak, keterangan
```

Aturan impor (hanya Admin, `POST /laporan/import`):

- Header file harus sama persis dengan hasil ekspor, dan ukuran maksimum 5 MB.
- Impor hanya **menambah barang baru**. Baris dengan `kode_barang` yang sudah ada ditolak sebagai konflik, dan data lama tidak diubah.
- `kode_kategori` harus sudah terdaftar. Jika `kode_rak` diisi, kode tersebut harus cocok dengan rak yang ada, dan jika dikosongkan barang masuk tanpa lokasi.
- Kolom `stok_minimum` di CSV **diabaikan** — sistem hitung ulang otomatis via `Barang::stokMinimumForKategori` (FRN/furniture = 0, lainnya = 10).
- Seluruh impor berjalan dalam satu transaksi dan menampilkan ringkasan jumlah data masuk, konflik, dan baris tidak valid. Ekspor (`GET /laporan/export`) boleh untuk Admin, Staff, dan Pimpinan.

## Testing

```bash
# Unit/Feature test (SQLite in-memory, tanpa setup database)
composer test

# End-to-end (Playwright)
npx playwright install chromium
npm run test:e2e
```

Skenario E2E ada di folder `tests-e2e/`. Workflow CI (`.github/workflows/e2e.yml`) menjalankannya otomatis pada push dan pull request ke `main`/`master`.

## Struktur Folder

```
app/
  Http/Controllers/   # Auth, Barang, Kategori, Rak, DenahArea, MutasiBarang,
                      # StockOpnameBarang, Pencarian, Laporan, Dashboard, User, Voice
  Http/Middleware/    # CheckRole, SecurityHeaders
  Models/             # Barang (stokMinimumForKategori, scopeStokMenipis), Kategori (bukuId),
                      # Rak (denah_area_id), DenahArea, MutasiBarang, StockOpnameBarang, User
database/
  migrations/         # Skema database (termasuk migrasi buku → barang)
  seeders/            # UserSeeder, DatabaseSeeder
resources/
  views/              # Blade: dashboard, barang, kategori, rak, denah-area,
                      # mutasi-barang, stock-opname-barang, pencarian, laporan, users, auth
  css/, js/           # Tailwind, dashboard.css, barcode-scanner.js
routes/web.php        # Seluruh route aplikasi (role:* + throttle login/reset, voice/transcribe)
tests/, tests-e2e/    # PHPUnit dan Playwright
```

## Catatan Pengembangan

- **Migrasi adalah kontrak**: jangan hapus atau ganti nama migrasi yang sudah dipakai, dan hindari perubahan skema yang menghilangkan data barang, rak, atau kategori yang sudah ada.
- Antarmuka berbahasa Indonesia dengan istilah teknis tetap dalam bahasa Inggris (SKU, barcode, stock opname, mutasi, rak, denah).
- Tampilan dirancang mobile-first karena staff memakai HP di lorong rak.
- `public/build` dihasilkan oleh `npm run build`, jadi jangan diedit manual.
- Dokumen acuan: `PRODUCT.md` (pengguna, prinsip, batasan), `DESIGN.md` (sistem visual), dan `AGENTS.md` (panduan perintah dan konvensi).

## Lisensi

Proyek ini dibangun di atas Laravel, yang berlisensi [MIT](https://opensource.org/licenses/MIT).
