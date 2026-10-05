import { test, expect } from '@playwright/test';

test.describe('Journey 5: Pelaporan & Export/Import CSV', () => {

  test('Happy Path: Membuka laporan dan mendownload CSV', async ({ page }) => {
    await page.goto('/laporan');
    await expect(page).toHaveURL('/laporan');
    await expect(page.getByRole('heading', { name: /Laporan Gudang/i })).toBeVisible();

    // Export CSV download event
    const downloadPromise = page.waitForEvent('download');
    await page.getByRole('link', { name: /Export CSV/i }).click();
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toMatch(/laporan.*\.csv/);
  });

  test('Failure State: Import CSV dengan format header salah', async ({ page }) => {
    await page.goto('/laporan');

    // Upload file CSV palsu dengan header salah
    const filePayload = {
      name: 'bad-format.csv',
      mimeType: 'text/csv',
      buffer: Buffer.from('invalid_header,wrong_col\n1,2,3'),
    };

    await page.locator('input[type="file"]').setInputFiles(filePayload);
    await page.getByRole('button', { name: /Import CSV/i }).click();

    // Harusnya dapat pesan error
    await expect(page.locator('.alert-error')).toBeVisible();
    await expect(page.locator('.alert-error')).toContainText(/Format CSV tidak sesuai/i);
  });

});
