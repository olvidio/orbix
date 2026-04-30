/**
 * Una sola vez por ejecución: inicia sesión y guarda cookies en `.auth/storage.json`.
 * Solo se invoca cuando existen `E2E_USER` y `E2E_PASSWORD` (ver `playwright.config.ts`).
 */
import fs from 'node:fs';
import path from 'node:path';
import { setTimeout as sleep } from 'node:timers/promises';

import dotenv from 'dotenv';
import { chromium } from '@playwright/test';

import type { Browser, FullConfig, Page } from '@playwright/test';

import { getPlaywrightBaseUrl } from './get-playwright-base-url';
import { diagnoseBackendHtml } from './backend-diagnostics';

dotenv.config({ path: path.resolve(__dirname, '.env'), override: false });

async function refuseIfBrokenPhpPage(
  page: Page,
  browser: Browser
): Promise<void> {
  const html = await page.content().catch(() => '');
  const diag = diagnoseBackendHtml(html);
  if (diag !== null) {
    await bailOut(`La app respondió pero no es usable para E2E: ${diag}`, page, browser);
  }
}

async function bailOut(
  message: string,
  page: Page,
  browser: Browser
): Promise<never> {
  const url = page.url();
  const title = await page.title().catch(() => '');
  const body =
    ((await page.textContent('body').catch(() => '')) ?? '')
      .replace(/\s+/g, ' ')
      .trim()
      .slice(0, 2500) || '(vacío)';
  await browser.close();
  throw new Error(`${message}\nURL: ${url}\nTítulo: ${title}\n--- cuerpo (recorte) ---\n${body}`);
}

export default async function globalSetup(_config: FullConfig): Promise<void> {
  void _config;
  const baseURL = getPlaywrightBaseUrl();
  const user = process.env.E2E_USER!;
  const pass = process.env.E2E_PASSWORD!;
  const loginTimeout = Number(process.env.E2E_LOGIN_TIMEOUT_MS ?? '90000');

  const authFile = path.resolve(__dirname, '.auth/storage.json');
  fs.mkdirSync(path.dirname(authFile), { recursive: true });

  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    baseURL,
    locale: process.env.E2E_LOCALE ?? 'es-ES',
    ignoreHTTPSErrors: true,
  });
  const page = await context.newPage();

  // Importante: no usar "/index.php" — con baseURL .../orbix el "/" inicial sustituye todo el path
  // y acaba en http://host/index.php (404). Debe ser relativo al base.
  const response = await page.goto('index.php', {
    waitUntil: 'load',
    timeout: 60_000,
  });

  if (response === null) {
    await bailOut('Sin respuesta HTTP al abrir index.php (entrada Orbix)', page, browser);
  }
  if (response.status() >= 400) {
    let detail = '';
    if (response.status() === 404) {
      try {
        const u = new URL(baseURL);
        const pathTrim = u.pathname.replace(/\/+$/, '') || '/';
        const pathOnlyRoot = pathTrim === '/' || pathTrim === '';
        if (pathOnlyRoot) {
          detail =
            `\n\nPLAYWRIGHT_BASE_URL parece sólo http(s)://host:puerto/ sin carpeta Orbix ` +
            `(petición efectiva suele ir a "${u.origin}/index.php").`;
        }
      } catch {
        /* noop */
      }
    }
    await bailOut(
      `HTTP ${response.status()} al cargar Orbix desde index.php.${detail}`,
      page,
      browser
    );
  }

  await refuseIfBrokenPhpPage(page, browser);

  const userField = page.locator('input[name="username"]');
  try {
    // 'attached' es más tolerante que 'visible' (overlays, animaciones, CSS raro).
    await userField.waitFor({ state: 'attached', timeout: loginTimeout });
  } catch {
    const html = await page.content().catch(() => '');
    const diag = diagnoseBackendHtml(html);
    const msg = diag
      ? `Orbix no mostró login: ${diag}`
      : `No apareció input[name="username"] en ${loginTimeout}ms. ` +
        `Si la app tarda mucho en arrancar, sube E2E_LOGIN_TIMEOUT_MS. ` +
        `Si ves otra pantalla (SSO, proxy, error PHP), comprueba la URL base y el servidor.`;
    await bailOut(msg, page, browser);
  }

  await userField.fill(user);
  await page.locator('input[name="password"]').fill(pass);

  const esquema = process.env.E2E_ESQUEMA?.trim() ?? '';
  if (esquema) {
    const selectEsquema = page.locator('select[name="esquema"]');
    if ((await selectEsquema.count()) > 0) {
      try {
        await selectEsquema.selectOption(esquema, { timeout: 8_000 });
      } catch (e) {
        const msg = e instanceof Error ? e.message : String(e);
        await bailOut(
          `E2E_ESQUEMA="${esquema}" no coincide con ningún <option> del desplegable (select[name="esquema"]). ` +
            `En ubicación **sv** los valores llevan sufijo **v** (ej. Schema H-dlb → H-dlbv); en **sf**, sufijo **f**. ` +
            `Copia el value exacto del HTML o de la BD. Underlying: ${msg}`,
          page,
          browser
        );
      }
    } else {
      const hiddenOrText = page.locator('input[name="esquema"]');
      if ((await hiddenOrText.count()) > 0) {
        await hiddenOrText.fill(esquema);
      }
    }
  }

  const code2fa = process.env.E2E_2FA_CODE ?? '';
  if (code2fa) {
    await page.locator('input[name="verification_code"]').fill(code2fa);
  }

  const submit = page.locator(
    '#frm_login input[type="submit"], form.form-signin input[type="submit"]'
  ).first();

  // Por defecto `click()` espera a que terminen las navegaciones encadenadas; en Orbix suelen seguir
  // requests en segundo plano y esa promesa agota timeout. No enganchamos navegación al clic.
  await submit.click({ noWaitAfter: true });

  await Promise.race([
    page.waitForLoadState('load', { timeout: loginTimeout }),
    page.locator('form#frm_login').waitFor({ state: 'detached', timeout: loginTimeout }),
    page.locator('form#frm_login').waitFor({ state: 'hidden', timeout: loginTimeout }),
  ]).catch(() => {
    /* Algunos despliegues repintan sin evento load fiable; seguimos y comprobamos el formulario. */
  });

  await sleep(400);

  const formLogin = page.locator('form#frm_login');
  if (await formLogin.isVisible().catch(() => false)) {
    const err = await page.locator('#div_error_login').innerText().catch(() => '');
    await bailOut(
      `Login rechazado o sesión no establecida (sigue el formulario). ` +
        `Mensaje: ${err.trim() || '(sin mensaje)'} — revisa usuario, contraseña, E2E_ESQUEMA o E2E_2FA_CODE.`,
      page,
      browser
    );
  }

  await context.storageState({ path: authFile });
  await browser.close();
}
