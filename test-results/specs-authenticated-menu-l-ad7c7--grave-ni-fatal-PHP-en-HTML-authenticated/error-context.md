# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: specs/authenticated/menu-links.spec.ts >> Tras login: enlaces en la página principal >> GET de enlaces mismo origen sin código HTTP grave ni fatal PHP en HTML
- Location: e2e/specs/authenticated/menu-links.spec.ts:61:7

# Error details

```
Error: Índice autenticado no es navegable: Error PDO/SQL durante el arranque (BD inaccesible o fallo antes del formulario). Revisa Postgres, credenciales y que el usuario de aplicación no haya perdido derechos.
URL: http://orbix.docker:8003/orbix/index.php
--- cuerpo (recorte) ---
( ! ) Fatal error: Uncaught PDOException: SQLSTATE[08006] [7] connection to server at "192.168.200.16", port 5432 failed: timeout expired in /var/www/orbix/src/shared/infrastructure/persistence/DBConnection.php on line 61 ( ! ) PDOException: SQLSTATE[08006] [7] connection to server at "192.168.200.16", port 5432 failed: timeout expired in /var/www/orbix/src/shared/infrastructure/persistence/DBConnection.php on line 61 Call Stack #TimeMemoryFunctionLocation 10.0062546656{main}( ).../index.php:0 20.10511901864require_once( '/var/www/orbix/src/shared/global_object.inc ).../index.php:34 30.17122270920src\shared\infrastructure\persistence\DBConnection->getPDO( ).../global_object.inc:176 40.17122271664__construct( $dsn = 'pgsql:host=192.168.200.16;port=5432;dbname=\'sf\';user=\'H-dlbf\';password=\'|X%y3j3H7h#\';sslmode=require;sslcert=/home/orbix/certs/aquinateClient.crt;sslkey=/home/orbix/certs/aquinateClient.key;sslrootcert=/home/orbix/certs/root.crt' ).../DBConnection.php:61
```

# Page snapshot

```yaml
- table [ref=e3]:
  - rowgroup [ref=e4]:
    - 'row "( ! ) Fatal error: Uncaught PDOException: SQLSTATE[08006] [7] connection to server at \"192.168.200.16\", port 5432 failed: timeout expired in /var/www/orbix/src/shared/infrastructure/persistence/DBConnection.php on line 61" [ref=e5]':
      - 'columnheader "( ! ) Fatal error: Uncaught PDOException: SQLSTATE[08006] [7] connection to server at \"192.168.200.16\", port 5432 failed: timeout expired in /var/www/orbix/src/shared/infrastructure/persistence/DBConnection.php on line 61" [ref=e6]'
    - 'row "( ! ) PDOException: SQLSTATE[08006] [7] connection to server at \"192.168.200.16\", port 5432 failed: timeout expired in /var/www/orbix/src/shared/infrastructure/persistence/DBConnection.php on line 61" [ref=e7]':
      - 'columnheader "( ! ) PDOException: SQLSTATE[08006] [7] connection to server at \"192.168.200.16\", port 5432 failed: timeout expired in /var/www/orbix/src/shared/infrastructure/persistence/DBConnection.php on line 61" [ref=e8]'
    - row "Call Stack" [ref=e9]:
      - columnheader "Call Stack" [ref=e10]
    - row "# Time Memory Function Location" [ref=e11]:
      - columnheader "#" [ref=e12]
      - columnheader "Time" [ref=e13]
      - columnheader "Memory" [ref=e14]
      - columnheader "Function" [ref=e15]
      - columnheader "Location" [ref=e16]
    - 'row "1 0.0062 546656 {main}( ) .../index.php:0" [ref=e17]':
      - cell "1" [ref=e18]
      - cell "0.0062" [ref=e19]
      - cell "546656" [ref=e20]
      - 'cell "{main}( )" [ref=e21]'
      - cell ".../index.php:0" [ref=e22]
    - row "2 0.1051 1901864 require_once( '/var/www/orbix/src/shared/global_object.inc ) .../index.php:34" [ref=e23]:
      - cell "2" [ref=e24]
      - cell "0.1051" [ref=e25]
      - cell "1901864" [ref=e26]
      - cell "require_once( '/var/www/orbix/src/shared/global_object.inc )" [ref=e27]
      - cell ".../index.php:34" [ref=e28]
    - row "3 0.1712 2270920 src\\shared\\infrastructure\\persistence\\DBConnection->getPDO( ) .../global_object.inc:176" [ref=e29]:
      - cell "3" [ref=e30]
      - cell "0.1712" [ref=e31]
      - cell "2270920" [ref=e32]
      - cell "src\\shared\\infrastructure\\persistence\\DBConnection->getPDO( )" [ref=e33]
      - cell ".../global_object.inc:176" [ref=e34]
    - row "4 0.1712 2271664 __construct( $dsn = 'pgsql:host=192.168.200.16;port=5432;dbname=\\'sf\\';user=\\'H-dlbf\\';password=\\'|X%y3j3H7h#\\';sslmode=require;sslcert=/home/orbix/certs/aquinateClient.crt;sslkey=/home/orbix/certs/aquinateClient.key;sslrootcert=/home/orbix/certs/root.crt' ) .../DBConnection.php:61" [ref=e35]:
      - cell "4" [ref=e36]
      - cell "0.1712" [ref=e37]
      - cell "2271664" [ref=e38]
      - cell "__construct( $dsn = 'pgsql:host=192.168.200.16;port=5432;dbname=\\'sf\\';user=\\'H-dlbf\\';password=\\'|X%y3j3H7h#\\';sslmode=require;sslcert=/home/orbix/certs/aquinateClient.crt;sslkey=/home/orbix/certs/aquinateClient.key;sslrootcert=/home/orbix/certs/root.crt' )" [ref=e39]:
        - link "__construct" [ref=e40] [cursor=pointer]:
          - /url: http://www.php.net/PDO.construct
        - text: ( $dsn = 'pgsql:host=192.168.200.16;port=5432;dbname=\'sf\';user=\'H-dlbf\';password=\'|X%y3j3H7h#\';sslmode=require;sslcert=/home/orbix/certs/aquinateClient.crt;sslkey=/home/orbix/certs/aquinateClient.key;sslrootcert=/home/orbix/certs/root.crt' )
      - cell ".../DBConnection.php:61" [ref=e41]
```

# Test source

```ts
  1   | import { expect, test } from '@playwright/test';
  2   | 
  3   | import {
  4   |   diagnoseBackendHtml,
  5   |   formatBackendFailureBody,
  6   | } from '../../backend-diagnostics';
  7   | import { discoverUrlsViaMenuAjaxClicks } from '../../menu-ajax-discovery';
  8   | import { collectOrbixUrls } from '../../orbix-url-collector';
  9   | import { clickGrupmenuLabelIfSet, indexPathForE2e } from '../../grupmenu';
  10  | 
  11  | /**
  12  |  * Orbix en Docker puede no tener BDU; la portada muestra entonces ese aviso pero login/smoke siguen siendo útiles.
  13  |  * Con `E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1`, este spec queda **skipped** si el diagnóstico es sólo BDU (no actives en CI donde quieras validar BDU).
  14  |  */
  15  | const SKIP_MENU_LINKS_IF_NO_BDU =
  16  |   process.env.E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE === '1';
  17  | 
  18  | function isBduOnlyDiagnostic(diagnostic: string): boolean {
  19  |   return /\bBDU\b/i.test(diagnostic);
  20  | }
  21  | 
  22  | /** Patrones típicos de página de error fatal en PHP (incl. Xdebug). */
  23  | const PHP_HTML_ERROR_SNIPPETS =
  24  |   /Fatal error:|\bxdebug-error\b|uncaught\s+exception|\( ?! ?\)|<b>Fatal\b/i;
  25  | 
  26  | function normalizeOrigin(urlStr: string): string {
  27  |   return new URL(urlStr).origin;
  28  | }
  29  | 
  30  | function shouldSkipHref(href: string, origin: string): boolean {
  31  |   let u: URL;
  32  |   try {
  33  |     u = new URL(href, origin + '/');
  34  |   } catch {
  35  |     return true;
  36  |   }
  37  |   if (u.origin !== origin) {
  38  |     return true;
  39  |   }
  40  |   if (!['http:', 'https:'].includes(u.protocol)) {
  41  |     return true;
  42  |   }
  43  |   const p = (u.pathname + u.search).toLowerCase();
  44  |   if (/\bsalir\b|\blogout\b|cerrar_?ses/i.test(p)) {
  45  |     return true;
  46  |   }
  47  |   const del = /\b(delete|eliminar)\b/;
  48  |   if (del.test(u.search) || del.test(pathOnlyLastSegment(u.pathname))) {
  49  |     return true;
  50  |   }
  51  |   return false;
  52  | }
  53  | 
  54  | function pathOnlyLastSegment(pathname: string): string {
  55  |   const trim = pathname.replace(/^\/+|\/+$/g, '');
  56  |   const parts = trim.split('/');
  57  |   return parts[parts.length - 1] ?? '';
  58  | }
  59  | 
  60  | test.describe('Tras login: enlaces en la página principal', () => {
  61  |   test('GET de enlaces mismo origen sin código HTTP grave ni fatal PHP en HTML', async ({
  62  |     page,
  63  |     baseURL,
  64  |   }) => {
  65  |     test.skip(!baseURL, 'Sin base URL en configuración.');
  66  |     await page.goto(indexPathForE2e());
  67  | 
  68  |     await expect(page.locator('form#frm_login')).toHaveCount(0);
  69  |     await expect(page.locator('body')).toBeAttached();
  70  | 
  71  |     const broken = diagnoseBackendHtml(await page.content());
  72  |     if (
  73  |       SKIP_MENU_LINKS_IF_NO_BDU &&
  74  |       broken !== null &&
  75  |       isBduOnlyDiagnostic(broken)
  76  |     ) {
  77  |       test.skip(
  78  |         true,
  79  |         `${broken} Omitido con E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1 (ver e2e/README.md).`,
  80  |       );
  81  |     }
  82  |     if (broken !== null) {
> 83  |       throw new Error(
      |             ^ Error: Índice autenticado no es navegable: Error PDO/SQL durante el arranque (BD inaccesible o fallo antes del formulario). Revisa Postgres, credenciales y que el usuario de aplicación no haya perdido derechos.
  84  |         `Índice autenticado no es navegable: ${broken}\n${await formatBackendFailureBody(page)}`
  85  |       );
  86  |     }
  87  | 
  88  |     await clickGrupmenuLabelIfSet(page);
  89  | 
  90  |     // Menú burger rellena el horizontal en DOMContentLoaded; legacy ya renderiza `#udm` en HTML.
  91  |     const menuHint =
  92  |       '[onclick*="fnjs_link_submenu"], a[href]:not([href="#"]):not([href=""])';
  93  |     try {
  94  |       await page.locator(menuHint).first().waitFor({ state: 'attached', timeout: 25_000 });
  95  |     } catch {
  96  |       const html = await page.content();
  97  |       const diag = diagnoseBackendHtml(html);
  98  |       const detail = await formatBackendFailureBody(page);
  99  |       if (
  100 |         SKIP_MENU_LINKS_IF_NO_BDU &&
  101 |         diag !== null &&
  102 |         isBduOnlyDiagnostic(diag)
  103 |       ) {
  104 |         test.skip(
  105 |           true,
  106 |           `${diag} Omitido con E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1.`,
  107 |         );
  108 |       }
  109 |       if (diag !== null) {
  110 |         throw new Error(`${diag}\n${detail}`);
  111 |       }
  112 |       throw new Error(
  113 |         `No apareció ningún ítem de menú Orbix en 25s (${menuHint}). ` +
  114 |           `¿Usuario sin entradas de menú o portada con error silencioso?\n${detail}`
  115 |       );
  116 |     }
  117 | 
  118 |     /** Origen de la página cargada (Host puede diferir del string en PLAYWRIGHT_BASE_URL). */
  119 |     const pageOrigin = normalizeOrigin(page.url());
  120 | 
  121 |     const hrefsIndex = await collectOrbixUrls(page);
  122 |     const hrefsFromMain = await discoverUrlsViaMenuAjaxClicks(page);
  123 |     const hrefs = [...new Set([...hrefsIndex, ...hrefsFromMain])];
  124 | 
  125 |     const max = Number(process.env.E2E_MAX_LINKS ?? '100');
  126 | 
  127 |     expect(baseURL).toBeTruthy();
  128 | 
  129 |     const candidates = [...new Set(hrefs)]
  130 |       .filter((h) => !shouldSkipHref(h, pageOrigin))
  131 |       .slice(0, max);
  132 | 
  133 |     expect(
  134 |       candidates.length,
  135 |       `No se recolectaron URLs navegables del mismo origen (${pageOrigin}). ` +
  136 |         `¿Menú vacío o solo \`href="#"\`/` +
  137 |         `sin fnjs_link_submenu? índice=${hrefsIndex.length} tras_clics_ajax=${hrefsFromMain.length} unión=${hrefs.length} antes del filtro.`
  138 |     ).toBeGreaterThan(0);
  139 | 
  140 |     const failures: string[] = [];
  141 | 
  142 |     for (const href of candidates) {
  143 |       try {
  144 |         const res = await page.request.get(href, {
  145 |           maxRedirects: 10,
  146 |           timeout: 55_000,
  147 |           failOnStatusCode: false,
  148 |         });
  149 |         const status = res.status();
  150 |         if (status >= 400) {
  151 |           failures.push(`${status} ${href}`);
  152 |           continue;
  153 |         }
  154 |         const ct = (res.headers()['content-type'] ?? '').toLowerCase();
  155 |         if (ct.includes('text/html')) {
  156 |           const body = await res.text();
  157 |           if (PHP_HTML_ERROR_SNIPPETS.test(body)) {
  158 |             failures.push(`php-fatal-pattern ${href}`);
  159 |           }
  160 |         }
  161 |       } catch (e: unknown) {
  162 |         const msg = e instanceof Error ? e.message : String(e);
  163 |         failures.push(`request-error ${href} (${msg.slice(0, 120)})`);
  164 |       }
  165 |     }
  166 | 
  167 |     expect(failures, failures.join('\n')).toEqual([]);
  168 |   });
  169 | });
  170 | 
```