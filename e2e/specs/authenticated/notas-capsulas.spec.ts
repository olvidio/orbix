import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, redEnCalma, sqlDev } from '../../orbix-app';

/**
 * Las acciones de `notas` que escriben (subir o quitar el PDF de un acta, añadir una persona al acta,
 * guardar una nota, copiar una tessera) solo se ejecutan con la cápsula `HashB` que firma su pantalla
 * (fase 6.5). Aquí se comprueba, sin guardar nada, que cada pantalla pinta su cápsula en el formulario
 * que la envía; que el endpoint la exige lo comprueban `NotasControllersContractTest` y
 * `ControladoresConTokenSinTokenTest`. El alta de una nota por la interfaz está en `notas-alta-baja`.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';

const capsula = /^[\w.%-]{20,}$/;

test('las pantallas de actas y de tessera pintan la cápsula de sus acciones', async ({ page }) => {
  const acta = sqlDev('sv', `SELECT acta FROM "${ESQUEMA_V}".e_actas_dl WHERE (pdf IS NULL OR length(pdf) = 0) AND acta LIKE '${ESQUEMA_V.replace(/^H-|v$/g, '')} %' ORDER BY f_acta DESC NULLS LAST LIMIT 1`);
  const idNom = sqlDev('sv', `SELECT id_nom FROM "${ESQUEMA_V}".p_numerarios WHERE situacion = 'A' ORDER BY id_nom LIMIT 1`);
  test.skip(acta === '' || idNom === '', 'hace falta un acta de la dl sin PDF y un numerario');

  const inc = await abrirApp(page);
  const main = page.locator('#main');

  // Acta: subir el PDF firmado (y, si el acta admite añadir personas, ese formulario).
  await abrirPantalla(page, `frontend/notas/controller/acta_ver.php?acta=${encodeURIComponent(acta)}`, 'form#frm_acta');
  await redEnCalma(page);
  await expect(main.locator('#frm_upload input[name="ctx_pdf_subir"]')).toHaveValue(capsula);
  const anadir = main.locator('#frm_add_persona input[name="ctx_add_persona"]');
  if ((await anadir.count()) > 0) {
    await expect(anadir).toHaveValue(capsula);
  }

  // Copiar tessera: la cápsula fija la persona de origen.
  await abrirPantalla(page, `frontend/notas/controller/tessera_copiar_select.php?sel[]=${idNom}%23n`, 'form#frm_sin_nombre');
  await expect(main.locator('#frm_sin_nombre input[name="ctx_copiar"]')).toHaveValue(capsula);
  await expect(main.locator('#frm_sin_nombre input[name="id_nom_org"]')).toHaveValue(idNom);

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
});
