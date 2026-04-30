import { setTimeout as sleep } from 'node:timers/promises';

import type { Page } from '@playwright/test';

import { collectOrbixUrls } from './orbix-url-collector';

/**
 * Anclas de menú que disparan `fnjs_link_submenu` (POST → HTML en `#main`).
 * Excluimos explícitamente `#main` para no re-hacer clic en enlaces del contenido cargado.
 */
const MENU_AJAX_ANCHOR_SELECTOR =
  '#udm a[onclick*="fnjs_link_submenu"], ' +
  '#submenu a[onclick*="fnjs_link_submenu"], ' +
  '.horizontal-menu > li > a[onclick*="fnjs_link_submenu"], ' +
  '.horizontal-menu .dropdown a[onclick*="fnjs_link_submenu"], ' +
  '.menu-utilidades-derecha a[onclick*="fnjs_link_submenu"]';

const DEFAULT_MAX_CLICKS = 30;

function shouldSkipMenuOnclick(onclick: string): boolean {
  if (!onclick.includes('fnjs_link_submenu')) {
    return true;
  }
  if (/\/programas\/pag_html_editar\.php/.test(onclick)) {
    return true;
  }
  if (/\b(salir|logout)\b/i.test(onclick)) {
    return true;
  }
  if (/\b(eliminar|delete)\b/i.test(onclick)) {
    return true;
  }
  return false;
}

/**
 * Con `E2E_MENU_AJAX_DISCOVER=1`, pulsa hasta N ítems del menú superior (mismo criterio que Orbix AJAX)
 * y acumula URLs vistas dentro de `#main` tras cada respuesta (enlaces `href` + `fnjs_link_submenu` incrustados).
 * Puede ejecutar POST con efectos en datos: usar sólo sobre entornos de prueba / usuario de solo lectura.
 */
export async function discoverUrlsViaMenuAjaxClicks(page: Page): Promise<string[]> {
  if (process.env.E2E_MENU_AJAX_DISCOVER !== '1') {
    return [];
  }

  const maxClicksRaw = Number(
    process.env.E2E_MAX_MENU_AJAX_CLICKS ?? DEFAULT_MAX_CLICKS
  );
  const maxClicks = Number.isFinite(maxClicksRaw)
    ? Math.min(200, Math.max(1, maxClicksRaw))
    : DEFAULT_MAX_CLICKS;

  const seen = new Set<string>();
  const items = page.locator(MENU_AJAX_ANCHOR_SELECTOR);
  const total = await items.count();
  const nPlan = Math.min(maxClicks, total);

  for (let i = 0; i < nPlan; i++) {
    const item = items.nth(i);
    const onclick = (await item.getAttribute('onclick')) ?? '';
    if (shouldSkipMenuOnclick(onclick)) {
      continue;
    }
    try {
      await item.scrollIntoViewIfNeeded();
      await item.click({ timeout: 12_000 });
    } catch {
      continue;
    }
    await page.waitForLoadState('networkidle', { timeout: 25_000 }).catch(() => {});
    await sleep(450);
    const fromMain = await collectOrbixUrls(page, '#main');
    for (const u of fromMain) {
      seen.add(u);
    }
  }

  return [...seen];
}
