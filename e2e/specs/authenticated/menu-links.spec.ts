import { expect, test } from '@playwright/test';

import {
  diagnoseBackendHtml,
  formatBackendFailureBody,
} from '../../backend-diagnostics';
import { discoverUrlsViaMenuAjaxClicks } from '../../menu-ajax-discovery';
import { collectOrbixUrls } from '../../orbix-url-collector';
import { clickGrupmenuLabelIfSet, indexPathForE2e } from '../../grupmenu';

/**
 * Orbix en Docker puede no tener BDU; la portada muestra entonces ese aviso pero login/smoke siguen siendo útiles.
 * Con `E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1`, este spec queda **skipped** si el diagnóstico es sólo BDU (no actives en CI donde quieras validar BDU).
 */
const SKIP_MENU_LINKS_IF_NO_BDU =
  process.env.E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE === '1';

function isBduOnlyDiagnostic(diagnostic: string): boolean {
  return /\bBDU\b/i.test(diagnostic);
}

/** Patrones típicos de página de error fatal en PHP (incl. Xdebug). */
const PHP_HTML_ERROR_SNIPPETS =
  /Fatal error:|\bxdebug-error\b|uncaught\s+exception|\( ?! ?\)|<b>Fatal\b/i;

function normalizeOrigin(urlStr: string): string {
  return new URL(urlStr).origin;
}

function shouldSkipHref(href: string, origin: string): boolean {
  let u: URL;
  try {
    u = new URL(href, origin + '/');
  } catch {
    return true;
  }
  if (u.origin !== origin) {
    return true;
  }
  if (!['http:', 'https:'].includes(u.protocol)) {
    return true;
  }
  const p = (u.pathname + u.search).toLowerCase();
  if (/\bsalir\b|\blogout\b|cerrar_?ses/i.test(p)) {
    return true;
  }
  const del = /\b(delete|eliminar)\b/;
  if (del.test(u.search) || del.test(pathOnlyLastSegment(u.pathname))) {
    return true;
  }
  return false;
}

function pathOnlyLastSegment(pathname: string): string {
  const trim = pathname.replace(/^\/+|\/+$/g, '');
  const parts = trim.split('/');
  return parts[parts.length - 1] ?? '';
}

test.describe('Tras login: enlaces en la página principal', () => {
  test('GET de enlaces mismo origen sin código HTTP grave ni fatal PHP en HTML', async ({
    page,
    baseURL,
  }) => {
    test.skip(!baseURL, 'Sin base URL en configuración.');
    await page.goto(indexPathForE2e());

    await expect(page.locator('form#frm_login')).toHaveCount(0);
    await expect(page.locator('body')).toBeAttached();

    const broken = diagnoseBackendHtml(await page.content());
    if (
      SKIP_MENU_LINKS_IF_NO_BDU &&
      broken !== null &&
      isBduOnlyDiagnostic(broken)
    ) {
      test.skip(
        true,
        `${broken} Omitido con E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1 (ver e2e/README.md).`,
      );
    }
    if (broken !== null) {
      throw new Error(
        `Índice autenticado no es navegable: ${broken}\n${await formatBackendFailureBody(page)}`
      );
    }

    await clickGrupmenuLabelIfSet(page);

    // Menú burger rellena el horizontal en DOMContentLoaded; legacy ya renderiza `#udm` en HTML.
    const menuHint =
      '[onclick*="fnjs_link_submenu"], a[href]:not([href="#"]):not([href=""])';
    try {
      await page.locator(menuHint).first().waitFor({ state: 'attached', timeout: 25_000 });
    } catch {
      const html = await page.content();
      const diag = diagnoseBackendHtml(html);
      const detail = await formatBackendFailureBody(page);
      if (
        SKIP_MENU_LINKS_IF_NO_BDU &&
        diag !== null &&
        isBduOnlyDiagnostic(diag)
      ) {
        test.skip(
          true,
          `${diag} Omitido con E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1.`,
        );
      }
      if (diag !== null) {
        throw new Error(`${diag}\n${detail}`);
      }
      throw new Error(
        `No apareció ningún ítem de menú Orbix en 25s (${menuHint}). ` +
          `¿Usuario sin entradas de menú o portada con error silencioso?\n${detail}`
      );
    }

    /** Origen de la página cargada (Host puede diferir del string en PLAYWRIGHT_BASE_URL). */
    const pageOrigin = normalizeOrigin(page.url());

    const hrefsIndex = await collectOrbixUrls(page);
    const hrefsFromMain = await discoverUrlsViaMenuAjaxClicks(page);
    const hrefs = [...new Set([...hrefsIndex, ...hrefsFromMain])];

    const max = Number(process.env.E2E_MAX_LINKS ?? '100');

    expect(baseURL).toBeTruthy();

    const candidates = [...new Set(hrefs)]
      .filter((h) => !shouldSkipHref(h, pageOrigin))
      .slice(0, max);

    expect(
      candidates.length,
      `No se recolectaron URLs navegables del mismo origen (${pageOrigin}). ` +
        `¿Menú vacío o solo \`href="#"\`/` +
        `sin fnjs_link_submenu? índice=${hrefsIndex.length} tras_clics_ajax=${hrefsFromMain.length} unión=${hrefs.length} antes del filtro.`
    ).toBeGreaterThan(0);

    const failures: string[] = [];

    for (const href of candidates) {
      try {
        const res = await page.request.get(href, {
          maxRedirects: 10,
          timeout: 55_000,
          failOnStatusCode: false,
        });
        const status = res.status();
        if (status >= 400) {
          failures.push(`${status} ${href}`);
          continue;
        }
        const ct = (res.headers()['content-type'] ?? '').toLowerCase();
        if (ct.includes('text/html')) {
          const body = await res.text();
          if (PHP_HTML_ERROR_SNIPPETS.test(body)) {
            failures.push(`php-fatal-pattern ${href}`);
          }
        }
      } catch (e: unknown) {
        const msg = e instanceof Error ? e.message : String(e);
        failures.push(`request-error ${href} (${msg.slice(0, 120)})`);
      }
    }

    expect(failures, failures.join('\n')).toEqual([]);
  });
});
