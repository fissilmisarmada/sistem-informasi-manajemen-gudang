import { test as setup, expect } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const authFile = path.join(__dirname, '../playwright/.auth/user.json');

setup('authenticate as admin', async ({ page }) => {
  await page.goto('/login');
  
  // Pilih role Admin via select-role
  await page.getByTestId('select-role').selectOption('admin@gmail.com');
  
  // Isi password
  await page.getByTestId('input-password').fill('admin123');
  
  // Submit
  await page.getByTestId('button-submit').click();

  // Tunggu redirect ke dashboard admin
  await expect(page).toHaveURL(/.*\/dashboard\/admin/);
  
  // Simpan storage state
  await page.context().storageState({ path: authFile });
});
