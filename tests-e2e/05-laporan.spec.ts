import { test, expect } from '@playwright/test';

test.describe('Journey 5: Pelaporan & Export/Import CSV', () => {

  test('Happy Path: Membuka laporan dan mendownload CSV', async ({ page, request }) => {
    await page.goto('/laporan');
    await expect(page).toHaveURL('/laporan');
    await expect(page.getByRole('heading', { name: /Laporan Gudang/i })).toBeVisible();

    // Export CSV via direct request (reuse authenticated session cookies)
    const cookies = await page.context().cookies();
    const cookieHeader = cookies.map(c => `${c.name}=${c.value}`).join('; ');
    const xsrf = cookies.find(c => c.name === 'XSRF-TOKEN')?.value ?? '';

    const response = await page.request.get('/laporan/export', {
      headers: {
        'Cookie': cookieHeader,
        'X-XSRF-TOKEN': decodeURIComponent(xsrf),
      },
    });

    expect(response.status()).toBe(200);
    expect(response.headers()['content-disposition']).toMatch(/attachment.*laporan.*\.csv/);
    const body = await response.text();
    expect(body).toContain('kode_barang');
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
    const alert = page.getByRole('alert');
    await expect(alert).toBeVisible();
    await expect(alert).toContainText(/Format CSV tidak sesuai/i);
  });

});
