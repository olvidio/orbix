import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujos de escritura de personas (fase 3.3), por la interfaz:
 *
 * 1. Numerario: buscar un apellido que no existe → «añadir persona» → guardar
 *    (`/src/personas/persona_update`) → está en la BD → abrir su ficha → modificarla → guardar.
 *    A un numerario no se le borra desde la ficha (solo se ofrece «eliminar» a las personas de
 *    paso): se limpia con SQL.
 * 2. Persona de paso de la propia dl: alta → abrir su ficha → «eliminar» con confirmación
 *    (`/src/personas/persona_eliminar`) → ya no está en la BD.
 *
 * Los selectores van por `onclick`/`name`, no por texto: la interfaz puede estar en otro idioma.
 * Los datos llevan una marca propia y se borran con SQL al final aunque el test falle a medias.
 */
const ESQUEMA = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const DL = (ESQUEMA.split('-')[1] ?? 'dlbv').replace(/v$/, '');
const marca = marcaE2e();

const numerario = (campos: string) => sqlDev('sv', `SELECT ${campos} FROM "${ESQUEMA}".p_numerarios WHERE apellido1 = '${marca}'`);
const dePaso = (campos: string) => sqlDev('sv', `SELECT ${campos} FROM restov.p_de_paso_ex WHERE apellido1 = '${marca}'`);

test.afterAll(() => {
  limpiar(
    () => sqlDev('sv', `DELETE FROM "${ESQUEMA}".p_numerarios WHERE apellido1 = '${marca}'`),
    () => sqlDev('sv', `DELETE FROM restov.p_de_paso_ex WHERE apellido1 = '${marca}'`),
  );
});

test('numerario: alta desde la búsqueda y modificación de la ficha', async ({ page }) => {
  const inc = await abrirApp(page);
  aceptarDialogos(page);

  await abrirPantalla(page, 'frontend/personas/controller/personas_que.php?tabla=p_numerarios&tipo=persona', '#frm_personas_que');
  await page.fill('#frm_personas_que #apellido1', marca);
  await page.click('#btn_ok');
  const anadir = page.locator('#main span.link_inv[onclick*="nuevo=1"]');
  await expect(anadir).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);

  await anadir.click();
  const ficha = page.locator('#main form#frm2');
  await expect(ficha).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await expect(ficha.locator('[name="apellido1"]')).toHaveValue(marca);
  await ficha.locator('[name="nom"]').fill('Prueba');
  await ficha.locator('[name="lugar_nacimiento"]').fill('Barcelona');
  const alta = await esperarSrc(page, 'persona_update', () => ficha.locator('th[onclick*="fnjs_guardar"]').click());
  expect(alta.success, alta.mensaje).toBe(true);

  const idNom = numerario('id_nom');
  expect(idNom, 'la persona está en la BD').toMatch(/^\d+$/);
  expect(numerario(`nom || '|' || lugar_nacimiento || '|' || situacion || '|' || id_tabla`)).toBe('Prueba|Barcelona|A|n');

  await abrirPantalla(page, `frontend/personas/controller/personas_editar.php?obj_pau=PersonaN&sel[]=${idNom}%23n`, 'form#frm2');
  await expect(ficha.locator('[name="nom"]')).toHaveValue('Prueba');
  await ficha.locator('[name="lugar_nacimiento"]').fill('Girona');
  await ficha.locator('[name="observ"]').fill('modificada por e2e');
  const cambio = await esperarSrc(page, 'persona_update', () => ficha.locator('th[onclick*="fnjs_guardar"]').click());
  expect(cambio.success, cambio.mensaje).toBe(true);
  expect(numerario(`id_nom || '|' || lugar_nacimiento || '|' || observ`)).toBe(`${idNom}|Girona|modificada por e2e`);

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});

test('persona de paso: alta y borrado desde su ficha', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);

  await abrirPantalla(page, `frontend/personas/controller/personas_editar.php?nuevo=1&obj_pau=PersonaEx&id_tabla=pn&apellido1=${marca}`, 'form#frm2');
  const ficha = page.locator('#main form#frm2');
  await ficha.locator('[name="nom"]').fill('Paso');
  await ficha.locator('[name="apellido1"]').fill(marca);
  await ficha.locator('select[name="dl"]').selectOption(DL);
  const alta = await esperarSrc(page, 'persona_update', () => ficha.locator('th[onclick*="fnjs_guardar"]').click());
  expect(alta.success, alta.mensaje).toBe(true);
  const idNom = dePaso('id_nom');
  expect(idNom, 'la persona de paso está en la BD').toMatch(/^-?\d+$/);
  expect(dePaso(`nom || '|' || dl`)).toBe(`Paso|${DL}`);

  await abrirPantalla(page, `frontend/personas/controller/personas_editar.php?obj_pau=PersonaEx&sel[]=${idNom}%23pn`, 'form#frm2');
  await expect(ficha.locator('[name="apellido1"]')).toHaveValue(marca);
  const borrado = await esperarSrc(page, 'persona_eliminar', () => ficha.locator('th[onclick*="fnjs_eliminar"]').click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(dePaso('id_nom'), 'ya no está en la BD').toBe('');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
