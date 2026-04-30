import type { Page } from '@playwright/test';

/** Ruta inicial: `index.php` o `index.php?id_grupmenu=N` (equiv. a fnjs_link_menu en scripts/index.js.php). */
export function indexPathForE2e(): string {
  const raw = process.env.E2E_GRUPMENU_ID?.trim();
  if (raw && /^\d+$/.test(raw)) {
    return `index.php?id_grupmenu=${raw}`;
  }
  return 'index.php';
}

function escapeRegExp(s: string): string {
  return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/**
 * Con varios grupmenús, legacy pinta `<li onclick="fnjs_link_menu('id')">nombre</li>` en `#menu`;
 * hamburguesa: enlaces en `#groupMenu` llaman a `setActiveGroup`.
 * Si ya definiste `E2E_GRUPMENU_ID`, no hace nada (la URL ya fijó el grupo).
 */
export async function clickGrupmenuLabelIfSet(page: Page): Promise<void> {
  const label = process.env.E2E_GRUPMENU_LABEL?.trim();
  if (!label) {
    return;
  }
  const id = process.env.E2E_GRUPMENU_ID?.trim();
  if (id && /^\d+$/.test(id)) {
    return;
  }

  const re = new RegExp(escapeRegExp(label), 'i');

  const legacy = page
    .locator('#menu > li[onclick*="fnjs_link_menu"]')
    .filter({ hasText: re });
  if ((await legacy.count()) > 0) {
    await legacy.first().click();
    await page.waitForLoadState('load');
    return;
  }

  const burger = page.locator('#groupMenu a').filter({ hasText: re });
  if ((await burger.count()) > 0) {
    await burger.first().click();
    await page
      .locator('[onclick*="fnjs_link_submenu"]')
      .first()
      .waitFor({ state: 'attached', timeout: 20_000 });
    return;
  }

  throw new Error(
    `E2E_GRUPMENU_LABEL="${label}": no aparece un grupmenu clicable en #menu (legacy) ni en #groupMenu (hamburguesa). ` +
      `Comprueba el texto en pantalla o usa E2E_GRUPMENU_ID con el id numérico del grupo.`
  );
}
