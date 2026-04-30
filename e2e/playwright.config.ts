import path from 'node:path';

import dotenv from 'dotenv';
import { defineConfig, devices } from '@playwright/test';

import { getPlaywrightBaseUrl } from './get-playwright-base-url';

/** Variables locales (no commitear `e2e/.env`). Lo ya exportado en shell o CI tiene prioridad. */
dotenv.config({ path: path.resolve(__dirname, '.env'), override: false });

const baseURL = getPlaywrightBaseUrl();

const authFile = path.resolve(__dirname, '.auth/storage.json');

const hasLoginCreds = Boolean(process.env.E2E_USER && process.env.E2E_PASSWORD);

export default defineConfig({
  timeout: 60_000,
  expect: { timeout: 15_000 },
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  workers: 1,
  reporter: [['list']],
  globalSetup: hasLoginCreds
    ? path.resolve(__dirname, 'global-setup.ts')
    : undefined,
  use: {
    baseURL,
    trace: process.env.E2E_TRACE === '1' ? 'on' : 'on-first-retry',
    screenshot: process.env.E2E_SCREENSHOT === '1' ? 'on' : 'off',
    video: process.env.E2E_VIDEO === '1' ? 'on' : 'off',
    ignoreHTTPSErrors: true,
  },
  projects: [
    {
      name: 'smoke',
      testMatch: /smoke-login-form\.spec\.ts$/,
      use: { ...devices['Desktop Chrome'] },
    },
    ...(hasLoginCreds
      ? [
          {
            name: 'authenticated',
            testMatch: /authenticated\/.*\.spec\.ts$/,
            use: {
              ...devices['Desktop Chrome'],
              storageState: authFile,
            },
          },
        ]
      : []),
  ],
});
