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

/** Log por consola y adjunta `menu-links-get.log` al informe HTML (`npx playwright show-report`). */
const MENU_LINK_GET_LOG = process.env.E2E_MENU_LINK_GET_LOG === '1';

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

/**
 * Muchos controllers Orbix están pensados para POST (HashFront); un GET suele esperar entrada o tardar hasta el timeout del servidor.
 * Por defecto no los probamos así. Para forzar todos los enlaces recolectados: `E2E_STRICT_MENU_LINK_GET=1` (mucho más lento / inestable).
 */
function shouldSkipBareGetOrbix(heuristicHref: string): boolean {
  if (process.env.E2E_STRICT_MENU_LINK_GET === '1') {
    return false;
  }
  let path: string;
  try {
    path = new URL(heuristicHref).pathname;
  } catch {
    return true;
  }
  const p = path.toLowerCase();
  // Sufijo *_que.php (p. ej. actividad_que.php) y prefijo que_*.php (p. ej. que_ctr_lista.php).
  if (/\/controller\/[^/]*_que\.php$/i.test(p)) return true;
  if (/\/controller\/que_[^/]*\.php$/i.test(p)) return true;
  if (/\/controller\/[^/]*_select\.php$/i.test(p)) return true;
  if (/\/preferencias\.php$/i.test(p)) return true;
  if (/\/public\/ayuda\/index\.php$/i.test(p)) return true;
  if (/\/avisos_generar\.php$/i.test(p)) return true;
  if (/\/gestion_/i.test(p)) return true;
  if (/\/incorporar_/i.test(p)) return true;
  if (/\/fases_/i.test(p)) return true;
  return false;
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
    const getTimeoutMs = Math.min(
      120_000,
      Math.max(3_000, Number(process.env.E2E_LINK_GET_TIMEOUT_MS ?? 18_000)),
    );

    expect(baseURL).toBeTruthy();

    const candidates = [...new Set(hrefs)]
      .filter((h) => !shouldSkipHref(h, pageOrigin))
      .filter((h) => !shouldSkipBareGetOrbix(h))
      .slice(0, max);

    expect(
      candidates.length,
      `No se recolectaron URLs navegables del mismo origen (${pageOrigin}) tras filtros (logout/delete y, por defecto, controllers tipo *_que.php / *_select.php y rutas POST-típicas). ` +
        `Para probarlos con GET poco fiable: E2E_STRICT_MENU_LINK_GET=1. ` +
        `¿Menú vacío? índice=${hrefsIndex.length} tras_clics_ajax=${hrefsFromMain.length} unión=${hrefs.length} antes del filtro.`
    ).toBeGreaterThan(0);

    const failures: string[] = [];
    const traceLines: string[] = [];

    const traceGet = (line: string) => {
      if (!MENU_LINK_GET_LOG) return;
      traceLines.push(line);
      // eslint-disable-next-line no-console -- opt-in (E2E_MENU_LINK_GET_LOG=1)
      console.log(`[menu-links-get] ${line}`);
    };

    traceGet(`candidates=${candidates.length} get_timeout_ms=${getTimeoutMs}`);

    for (const href of candidates) {
      try {
        const res = await page.request.get(href, {
          maxRedirects: 10,
          timeout: getTimeoutMs,
          failOnStatusCode: false,
        });
        const status = res.status();
        if (status >= 400) {
          traceGet(`FAIL http ${status} ${href}`);
          failures.push(`${status} ${href}`);
          continue;
        }
        const ct = (res.headers()['content-type'] ?? '').toLowerCase();
        if (ct.includes('text/html')) {
          const body = await res.text();
          if (PHP_HTML_ERROR_SNIPPETS.test(body)) {
            traceGet(`FAIL php-html-pattern ${href}`);
            failures.push(`php-fatal-pattern ${href}`);
            continue;
          }
        }
        traceGet(`OK ${status} ${href}`);
      } catch (e: unknown) {
        const msg = e instanceof Error ? e.message : String(e);
        traceGet(`FAIL request ${href} (${msg.slice(0, 160)})`);
        failures.push(`request-error ${href} (${msg.slice(0, 120)})`);
      }
    }

    if (MENU_LINK_GET_LOG) {
      const ok = traceLines.filter((l) => l.startsWith('OK ')).length;
      const fail = traceLines.filter((l) => l.startsWith('FAIL ')).length;
      traceGet(`summary ok=${ok} fail=${fail}`);
      await test.info().attach('menu-links-get.log', {
        body: traceLines.join('\n'),
        contentType: 'text/plain',
      });
    }

    expect(failures, failures.join('\n')).toEqual([]);
  });
});
