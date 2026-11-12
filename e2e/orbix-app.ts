import { expect, type Page, type Response } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import { diagnoseBackendHtml } from './backend-diagnostics';
import { indexPathForE2e } from './grupmenu';

/**
 * Ayudas para specs de flujos dentro de la aplicación (pantallas cargadas en `#main` de `index.php`).
 *
 * - `abrirApp()` abre `index.php` y empieza a recoger errores JS y respuestas de `/src` que no son JSON.
 * - `abrirPantalla()` carga un controlador frontend en `#main` como hace el menú (`fnjs_update_div`).
 * - `esperarSrc()` espera la respuesta JSON de un endpoint `/src/...` (las peticiones del navegador pasan por
 *   `frontend/shared/controller/src_ajax.php?…`).
 * - `sqlDev()` ejecuta SQL en la BD de desarrollo (contenedor `orbix-db-1`) para limpiar lo que un test haya podido dejar.
 */

export interface Incidencias {
  erroresJs: string[];
  respuestasNoJson: string[];
}

export async function abrirApp(page: Page): Promise<Incidencias> {
  const inc: Incidencias = { erroresJs: [], respuestasNoJson: [] };
  page.on('pageerror', (e) => inc.erroresJs.push(e.message));
  page.on('response', async (r: Response) => {
    const url = r.url();
    if (!/src_ajax\.php|\/src\//.test(url) || r.request().method() !== 'POST') {
      return;
    }
    const body = await r.text().catch(() => '');
    try {
      JSON.parse(body);
    } catch {
      inc.respuestasNoJson.push(`${r.status()} ${url}: ${body.slice(0, 200)}`);
    }
  });
  await page.goto(indexPathForE2e());
  await expect(page.locator('#main')).toBeAttached();
  return inc;
}

/**
 * Carga `path` (relativo a la raíz de Orbix, con su query) en `#main` y espera a que pinte `esperar`.
 * `soloPresente`: basta con que exista (p. ej. casillas de una rejilla SlickGrid, que Playwright no
 * siempre da por visibles).
 */
export async function abrirPantalla(page: Page, path: string, esperar: string, soloPresente = false): Promise<void> {
  await page.evaluate((p) => {
    // @ts-expect-error función global de Orbix (scripts/index.js.php)
    fnjs_update_div('#main', p);
  }, path);
  const loc = page.locator('#main').locator(esperar).first();
  if (soloPresente) {
    await expect(loc).toBeAttached({ timeout: 20_000 });
  } else {
    await expect(loc).toBeVisible({ timeout: 20_000 });
  }
  await sinErrorDeBackend(page);
}

/** Falla con un mensaje útil si `#main` contiene un fatal de PHP / PDO. */
export async function sinErrorDeBackend(page: Page): Promise<void> {
  const html = await page.locator('#main').innerHTML();
  const diag = diagnoseBackendHtml(html);
  expect(diag, diag ?? '').toBeNull();
  expect(html).not.toMatch(/Fatal error|Uncaught |Warning: |Notice: /);
}

/** Espera la respuesta del endpoint `/src/<ruta>` y devuelve su JSON. */
export async function esperarSrc(page: Page, ruta: string, accion: () => Promise<unknown>): Promise<{ success?: boolean; mensaje?: string; data?: unknown }> {
  const enc = encodeURIComponent(ruta);
  const [resp] = await Promise.all([
    page.waitForResponse((r) => r.url().includes(ruta) || r.url().includes(enc), { timeout: 30_000 }),
    accion(),
  ]);
  const body = await resp.text();
  try {
    return JSON.parse(body);
  } catch {
    throw new Error(`${ruta} no respondió JSON (${resp.status()}): ${body.slice(0, 300)}`);
  }
}

/** Acepta el siguiente `confirm`/`alert` y devuelve su texto. */
export function aceptarDialogos(page: Page): string[] {
  const textos: string[] = [];
  page.on('dialog', async (d) => {
    textos.push(d.message());
    await d.accept();
  });
  return textos;
}

/** SQL en la BD de desarrollo (solo para limpiar datos del propio test). */
export function sqlDev(db: string, sql: string): string {
  return execFileSync('docker', ['exec', 'orbix-db-1', 'psql', '-U', 'postgres', '-d', db, '-Atc', sql], { encoding: 'utf8' }).trim();
}

/** Marca alfabética única para los datos de un test (los VO de nombres no admiten dígitos). */
export function marcaE2e(): string {
  const letras = 'abcdefghijklmnopqrstuvwxyz';
  let n = Date.now();
  let s = '';
  while (n > 0) {
    s = letras[n % 26] + s;
    n = Math.floor(n / 26);
  }
  return 'Zze' + s;
}
