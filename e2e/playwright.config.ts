import path from 'node:path';

import dotenv from 'dotenv';
import { defineConfig, devices } from '@playwright/test';

import { getPlaywrightBaseUrl } from './get-playwright-base-url';

/** Variables locales (no commitear `e2e/.env`). Lo ya exportado en shell o CI tiene prioridad. */
dotenv.config({ path: path.resolve(__dirname, '.env'), override: false });

const baseURL = getPlaywrightBaseUrl();

const authFile = path.resolve(__dirname, '.auth/storage.json');

const hasLoginCreds = Boolean(process.env.E2E_USER && process.env.E2E_PASSWORD);

/** Presupuesto de tiempo del proyecto authenticated: index/menús/AJAX + GET secuencial a candidatos. */
function authenticatedProjectTimeoutMs(): number {
  const maxLinks = Math.min(500, Math.max(1, Number(process.env.E2E_MAX_LINKS ?? 100)));
  const getMs = Math.min(
    120_000,
    Math.max(3_000, Number(process.env.E2E_LINK_GET_TIMEOUT_MS ?? 18_000)),
  );
  const linkPhase = maxLinks * (getMs + 2_500);

  let preambleMs = 120_000;
  if (process.env.E2E_MENU_AJAX_DISCOVER === '1') {
    const raw = Number(process.env.E2E_MAX_MENU_AJAX_CLICKS ?? 30);
    const capped = Math.min(200, Math.max(1, Number.isFinite(raw) ? raw : 30));
    const settle = Number(process.env.E2E_MENU_AJAX_SETTLE_MS ?? 2500);
    const settleMs = Number.isFinite(settle) ? Math.min(30_000, Math.max(400, settle)) : 2500;
    preambleMs += capped * (12_000 + settleMs + 8000);
  }

  const total = preambleMs + linkPhase + 45_000;
  return Math.min(3_600_000, Math.max(180_000, total));
}

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
            timeout: authenticatedProjectTimeoutMs(),
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
