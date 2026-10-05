import { test, expect } from '@playwright/test';

test.describe('Journey 1: Autentikasi & Session Handling', () => {

  test('Happy Path: Admin bisa login dan logout dengan sukses', async ({ page }) => {
    await page.goto('/login');

    // Select role Admin via data-testid / role
    await page.getByTestId('select-role').selectOption('admin@gmail.com');
    await page.getByTestId('input-password').fill('admin123');
    await page.getByTestId('button-submit').click();

    // Pastikan masuk ke dashboard admin
    await expect(page).toHaveURL('/dashboard/admin');
    await expect(page.locator('body')).toContainText(/ADMINISTRATOR|ADMIN|Beranda/i);

    // Logout
    await page.getByRole('button', { name: /Keluar/i }).click();
    await expect(page).toHaveURL('/login');
  });

  test('Failure State: Password salah menampilkan error alert', async ({ page }) => {
    await page.goto('/login');

    await page.getByTestId('select-role').selectOption('admin@gmail.com');
    await page.getByTestId('input-password').fill('wrongpassword123');
    await page.getByTestId('button-submit').click();

    // Tetap di /login dan menampilkan error
    await expect(page).toHaveURL('/login');
    await expect(page.locator('.andon-alert--err')).toBeVisible();
    await expect(page.locator('.andon-alert--err')).toContainText(/salah/i);
  });

  test('Session Handling: Mengakses dashboard terproteksi tanpa login akan di-redirect', async ({ page }) => {
    // Tanpa storageState (logout context)
    await page.goto('/dashboard/admin');
    await expect(page).toHaveURL('/login');
  });

});
