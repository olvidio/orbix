import { expect, test } from '@playwright/test';

import { abrirApp } from '../../orbix-app';

/**
 * Exportar un bloque de la pantalla (formulario `frm_export` de la portada): la descarga la sirve
 * `frontend/shared/controller/exportar.php`, con sesión y con la firma del formulario. El título solo da
 * nombre al fichero, y el exportador antiguo de `libs/` (se ejecutaba sin sesión) ya no existe.
 */
test.describe.configure({ timeout: 120_000 });

const TABLA = '<div><table><tr><th>a</th></tr><tr><td>uno</td></tr></table></div>';

async function exportar(page: import('@playwright/test').Page, tipo: string, titulo: string) {
  const [descarga] = await Promise.all([
    page.waitForEvent('download', { timeout: 30_000 }),
    page.evaluate(
      ([t, n, html]) => {
        const valor = (id: string, v: string) => ((document.getElementById(id) as HTMLInputElement).value = v);
        valor('frm_export_tipo', t);
        valor('frm_export_titulo', n);
        valor('frm_export_enc', 'b64');
        valor('frm_export_ex', btoa(html));
        (document.getElementById('frm_export') as HTMLFormElement).submit();
      },
      [tipo, titulo, TABLA],
    ),
  ]);
  return descarga;
}

test('exportar: la descarga llega con el nombre saneado y en el formato pedido', async ({ page }) => {
  await abrirApp(page);

  const html = await exportar(page, 'html', 'lista de prueba');
  expect(html.suggestedFilename()).toBe('lista_de_prueba.html');

  const ods = await exportar(page, 'odfc', 'a;id;false');
  expect(ods.suggestedFilename()).toBe('a_id_false.ods');
  const ruta = await ods.path();
  expect(require('node:fs').readFileSync(ruta).subarray(0, 2).toString()).toBe('PK');

  const odt = await exportar(page, 'odft', 'texto');
  expect(odt.suggestedFilename()).toBe('texto.odt');
});

test('exportar: sin sesión no hay descarga, sin firma se rechaza y el exportador antiguo no existe', async ({ page, playwright, baseURL }) => {
  await abrirApp(page);
  const base = new URL(page.url());
  const raiz = base.origin + base.pathname.replace(/\/[^/]*$/, '');
  const form = { frm_export_tipo: 'odfc', frm_export_titulo: 'a;id;false', frm_export_ex: 'x' };

  const anonimo = await playwright.request.newContext();
  const sinSesion = await anonimo.post(`${raiz}/frontend/shared/controller/exportar.php`, { form });
  expect(sinSesion.headers()['content-disposition'] ?? '').toBe('');
  expect(await sinSesion.text()).not.toContain('uid=');
  const antiguo = await anonimo.post(`${raiz}/libs/export/export.php`, { form });
  expect(antiguo.status()).toBe(404);
  await anonimo.dispose();

  const sinFirma = await page.request.post(`${raiz}/frontend/shared/controller/exportar.php`, { form });
  expect(sinFirma.status()).toBe(403);
});
