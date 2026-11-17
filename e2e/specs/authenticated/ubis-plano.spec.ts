import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Plano de una dirección de una casa de la dl, por la interfaz (ficha `direcciones_editar`):
 * «adjuntar plano» → ventana → subir (`/src/ubis/plano_subir`) → «ver plano» descarga el mismo fichero
 * (`/src/ubis/plano_download?tk=`) → «eliminar plano» con confirmación (`/src/ubis/plano_borrar`).
 *
 * Usa una dirección que no tenga plano y al final la deja sin plano.
 */
const ESQUEMA = process.env.E2E_ESQUEMA_COMUN ?? 'H-dlb';
const PLANO = Buffer.concat([Buffer.from([0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a]), Buffer.from('plano e2e áé \x00\x01\x02')]);

let idUbi = '';
let idDireccion = '';

const plano = () =>
  sqlDev('comun', `SELECT coalesce(plano_nom, '') || '.' || coalesce(plano_extension, '') || '|' || coalesce(length(plano_doc), 0) FROM "${ESQUEMA}".u_dir_cdc_dl WHERE id_direccion = ${idDireccion}`);

test.beforeAll(() => {
  const fila = sqlDev(
    'comun',
    `SELECT x.id_ubi || '|' || x.id_direccion FROM "${ESQUEMA}".u_cross_cdc_dl_dir x JOIN "${ESQUEMA}".u_dir_cdc_dl d USING (id_direccion) ` +
      `JOIN "${ESQUEMA}".u_cdc_dl c USING (id_ubi) WHERE d.plano_doc IS NULL ORDER BY x.id_ubi LIMIT 1`,
  );
  [idUbi, idDireccion] = fila.split('|');
});

test.afterAll(() => {
  if (idDireccion !== '') {
    limpiar(() =>
      sqlDev('comun', `UPDATE "${ESQUEMA}".u_dir_cdc_dl SET plano_nom = NULL, plano_extension = NULL, plano_doc = NULL WHERE id_direccion = ${idDireccion}`),
    );
  }
});

test('plano de una dirección: subir, ver y quitar', async ({ page }) => {
  expect(idDireccion, 'hay una dirección de casa de la dl sin plano').toMatch(/^\d+$/);
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');

  await abrirPantalla(
    page,
    `frontend/ubis/controller/direcciones_editar.php?id_ubi=${idUbi}&obj_dir=DireccionCdcDl&id_direccion=${idDireccion}&idx=0`,
    'form#frm1',
  );
  await sinErrorDeBackend(page);

  // --- Adjuntar: se abre una ventana, se elige el fichero y se cierra sola al subirlo
  const [ventana] = await Promise.all([page.waitForEvent('popup'), main.locator('td[onclick*="fnjs_adjuntar"]').click()]);
  ventana.on('dialog', (d) => d.accept());
  await ventana.locator('#userfile').setInputFiles({ name: 'plano_e2e.png', mimeType: 'image/png', buffer: PLANO });
  const subida = ventana.waitForResponse((r) => r.url().includes('plano_subir'));
  await ventana.locator('#B1').click();
  // Al subir bien, la ventana se cierra sola (antes de poder leer la respuesta).
  expect((await subida).status()).toBe(200);
  await expect.poll(() => ventana.isClosed(), { timeout: 10_000 }).toBe(true);
  expect(plano()).toBe(`plano_e2e.png|${PLANO.length}`);

  // --- Ver plano: la ficha comprueba que hay plano y abre la descarga firmada, que es el mismo fichero
  const comprobar = page.waitForResponse((r) => r.url().includes('plano_bytea.php'));
  const descarga = page.waitForEvent('popup');
  await main.locator('td[onclick*="fnjs_ver_documento"]').click();
  const cuerpo = await (await comprobar).json();
  const datos = typeof cuerpo.data === 'string' ? JSON.parse(cuerpo.data) : cuerpo.data;
  expect(datos.exists, 'la ficha ve el plano').toBe(true);
  expect(datos.url).toContain('/src/ubis/plano_download?tk=');
  await (await descarga).close();
  const fichero = await page.request.get(datos.url);
  expect(fichero.status()).toBe(200);
  expect(Buffer.from(await fichero.body()).equals(PLANO), 'se descarga byte a byte lo que se subió').toBe(true);
  const sinToken = await page.request.get(datos.url.replace(/tk=[^&]+/, 'tk=x'));
  expect(sinToken.status(), 'sin un token válido no se descarga').toBe(400);

  // --- Eliminar plano: pide confirmación y lo quita
  const antes = dialogos.length;
  const borrado = await esperarSrc(page, 'plano_borrar', () => main.locator('td[onclick*="fnjs_eliminar_plano"]').click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de quitar el plano').toBeGreaterThan(antes);
  expect(plano()).toBe('.|0');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
