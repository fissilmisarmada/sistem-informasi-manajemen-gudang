import { test, expect } from '@playwright/test';

test.describe('Journey 4: Denah Gudang & Penempatan Rak', () => {

  test('Happy Path: Membuka denah gudang dan daftar rak', async ({ page }) => {
    await page.goto('/denah-gudang');
    await expect(page).toHaveURL('/denah-gudang');
    await expect(page.locator('body')).toContainText(/Denah Gudang/i);

    // Buka kelola rak
    await page.goto('/rak');
    await expect(page).toHaveURL('/rak');
    await expect(page.locator('body')).toContainText(/Kelola Lokasi Rak/i);
  });

});
