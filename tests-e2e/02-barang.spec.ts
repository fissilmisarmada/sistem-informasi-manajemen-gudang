import { test, expect } from '@playwright/test';

test.describe('Journey 2: Manajemen Barang & Stok Menipis', () => {

  test('Happy Path: Admin dapat melihat daftar barang dan detail barang', async ({ page }) => {
    await page.goto('/barang');

    // Pastikan halaman barang terbuka
    await expect(page).toHaveURL('/barang');
    await expect(page.locator('body')).toContainText(/Barang Gudang/i);

    // Halaman Stok Menipis
    await page.goto('/barang/stok-menipis');
    await expect(page).toHaveURL('/barang/stok-menipis');
    await expect(page.locator('body')).toContainText(/Stok Menipis/i);
  });

  test('Happy Path & Failure State: Tambah barang baru dan validasi form', async ({ page }) => {
    await page.goto('/barang/tambah/baru');
    await expect(page).toHaveURL('/barang/tambah/baru');

    // Failure state: Submit form kosong (HTML5 validation / server error)
    await page.getByRole('button', { name: /Simpan Barang/i }).click();
    await expect(page).toHaveURL('/barang/tambah/baru');

    // Happy Path: Isi data barang unik
    const uniqueKode = 'TEST-E2E-' + Date.now();
    await page.locator('input[name="kode_barang"]').fill(uniqueKode);
    await page.locator('input[name="nama"]').fill('Barang E2E Test');
    await page.locator('select[name="kategori_id"]').selectOption({ index: 1 });
    await page.locator('input[name="satuan"]').fill('pcs');
    await page.locator('input[name="stok"]').fill('50');
    await page.locator('input[name="stok_minimum"]').fill('5');

    await page.getByRole('button', { name: /Simpan Barang/i }).click();

    // Pastikan kembali ke daftar barang dan item baru ada di tabel/list
    await expect(page).toHaveURL('/barang');
    await expect(page.locator('body')).toContainText(uniqueKode);
  });

});
