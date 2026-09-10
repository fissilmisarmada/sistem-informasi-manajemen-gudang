# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

**Primer: Staff Gudang Operasional** — sehari-hari di lantai gudang/area rak, membawa HP untuk scan barcode. Job-to-be-done: menemukan barang dengan cepat, mencatat mutasi masuk/keluar, dan menuntaskan stock opname tanpa selisih. Keberhasilan = barang ketemu dalam detik, stok tercatat akurat, audit trail jelas.

**Sekunder:**
- **Admin Gudang** — kelola master data (barang, kategori, rak, denah area, users) dan jaga konsistensi data/migrasi existing.
- **Pimpinan** — memantau ringkasan stok, aktivitas, dan laporan untuk pengambilan keputusan; tidak mengedit data operasional.

## Product Purpose

Sistem Informasi Manajemen Gudang untuk mengelola barang secara terstruktur: dari input/pencarian, penempatan berbasis rak & denah area visual, mutasi, sampai stock opname. Tujuannya mengurangi waktu pencarian, mencegah selisih stok, dan memberi visibilitas operasional yang dapat dipercaya. Sukses = staff menyelesaikan alur "cari → temukan lokasi → catat mutasi/opname" dengan cepat di desktop maupun HP.

## Positioning

Beda dari inventaris generik: **penempatan berbasis denah & rak visual + barcode**. Barang tidak hanya tercatat, tapi dipetakan ke lokasi fisik (rak/denah area) yang bisa dilihat di peta gudang dan dicapai lewat scan barcode di lapangan. Pimpinan mendapat konteks lokasi, bukan sekadar angka stok.

## Operating Context

- Alur inti: login berbasis role → dashboard per peran → input/pencarian barang (barcode/manual) → penempatan ke rak/denah → mutasi & stock opname → laporan.
- Lingkungan: gudang fisik dengan rak dan area; penggunaan HP di lorong rak untuk scan.
- Peran & akses: `admin`, `staff` (staff_gudang), `pimpinan`; pembatasan via middleware `role:*`.
- Perangkat: scanner barcode/HP kamera sebagai sumber input utama di lapangan.
- Dokumen/laporan: ekspor/impor laporan, riwayat mutasi & opname.

## Capabilities and Constraints

**Kapabilitas terkonfirmasi (dari routes & kode):**
- Autentikasi & manajemen user (role admin/staff/pimpinan), lupa/reset password.
- Master barang, kategori, rak, denah area (CRUD + assign barang ke lokasi + update posisi).
- Denah gudang visual, pencarian & input barang (lookup barcode + input manual), mutasi barang, stock opname per barang + riwayat, laporan export/import.

**Batasan & keputusan yang mengikat:**
- Data & migrasi existing (barang/rak/kategori) wajib dipertahankan — tidak boleh ada penghapusan atau perubahan skema yang menghilangkan data.
- Web responsif (mobile web): harus nyaman di HP untuk scan di rak, bukan native app.
- Bahasa antarmuka: Indonesia dengan istilah teknis tetap English (SKU, barcode, stock opname, mutasi, rak, denah).
- Stack existing: Laravel + Vite + Tailwind — dipertahankan.
- Belum diputuskan: hosting/deploy target spesifik, merek scanner spesifik, kebutuhan offline/PWA.

## Brand Commitments

- Nama: Sistem Informasi Manajemen Gudang (dipertahankan).
- Voice: operasional, jelas, ringkas — istilah gudang yang familiar (Rak, Denah, Mutasi, Stock Opname, Pencarian).
- Belum ada palet/logo/typography yang mengikat secara brand — belum direkam sebagai constraint visual.

## Evidence on Hand

- Codebase Laravel runnable di `routes/web.php`, controller/model/migration existing, seeder `DatabaseSeeder`/`UserSeeder`.
- View Blade untuk dashboard per role, denah, barang, pencarian input, mutasi, stock opname.
- `resources/js/barcode-scanner.js` untuk pemindaian.
- Tidak ada testimoni/case study/press — jangan difabrikasi.

## Product Principles

1. **Lokasi dulu, angka belakangan** — setiap barang harus dapat dilacak ke rak/area dalam satu langkah.
2. **Scan mengalahkan ketik** — alur barcode adalah jalur utama; input manual hanya fallback.
3. **Akurasi stok adalah kebenaran** — mutasi & opname harus meninggalkan audit trail yang dapat dipercaya pimpinan.
4. **Responsif di lorong, bukan hanya di meja** — interaksi kritis harus lolos uji HP satu tangan.
5. **Jaga data existing** — perubahan tidak boleh mengorbankan migrasi/data yang sudah berjalan.

## Accessibility & Inclusion

- Target aksesibilitas: web responsif yang dapat dipakai di HP dengan kontras memadai dan target sentuh yang cukup untuk penggunaan di gudang. Belum ada standar formal (mis. WCAG level) yang ditetapkan — perlu dikonfirmasi bila ada regulasi internal.
