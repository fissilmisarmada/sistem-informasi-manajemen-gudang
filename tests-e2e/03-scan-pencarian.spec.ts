import { test, expect } from '@playwright/test';

test.describe('Journey 3: Pencarian Global & Scan Barcode', () => {

  test('Happy Path: Pencarian global barang', async ({ page }) => {
    await page.goto('/cari');
    await expect(page).toHaveURL('/cari');
    await expect(page.locator('body')).toContainText(/Pencarian/i);

    // Ketik query pencarian
    await page.locator('input[name="q"]').fill('Barang');
    await page.locator('button:has-text("Cari")').first().click();

    await expect(page).toHaveURL(/.*q=Barang.*/);
  });

  test('Happy Path & Fallback: Halaman Scan Barcode Input', async ({ page }) => {
    await page.goto('/input-barang');
    await expect(page).toHaveURL('/input-barang');

    // Cek form scan ada
    await expect(page.locator('#kode_barang')).toBeVisible();

    // Scan kode barang tidak dikenal -> memicu form manual
    await page.locator('#kode_barang').fill('UNREGISTERED-99999');
    await page.locator('select[name="kategori_id"]').first().selectOption({ index: 1 });
    await page.locator('select[name="rak_id"]').first().selectOption({ index: 1 });
    await page.locator('#jumlah').fill('10');
    await page.getByRole('button', { name: /Proses Barcode/i }).click();

    // Harusnya muncul peringatan bahwa barcode belum dikenali
    await expect(page.locator('body')).toContainText(/belum dikenali|manual/i);
  });

});
