import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sqlDev } from '../../orbix-app';

/**
 * Exportar los menús a una plantilla (fase 6.5): «exportar menús» → nombre → guardar
 * (`menus_exportar`, con la cápsula que firma el formulario) → la plantilla está en la BD con sus menús.
 *
 * La plantilla lleva la marca del test y se borra al final (sus menús cuelgan de ella y se van con ella).
 */
const NOMBRE = `${marcaE2e()} plantilla`;
const T = 'public.aux_templates_menus';

test.afterAll(() => {
  limpiar(() => sqlDev('comun', `DELETE FROM ${T} WHERE nombre = '${NOMBRE}'`));
});

test('menús: exportar a una plantilla nueva', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const form = page.locator('#main form#frm_menus');

  await abrirPantalla(page, 'frontend/menus/controller/menus_exportar_form.php', 'form#frm_menus');
  await expect(form.locator('input[name="ctx_exportar"]')).toHaveValue(/^[\w.%-]{20,}$/);
  await form.locator('input[name="nombre"]').fill(NOMBRE);
  const exportado = await esperarSrc(page, 'menus_exportar', () => form.locator('#btn_ok').click());
  expect(exportado.success, exportado.mensaje).toBe(true);

  const idPlantilla = sqlDev('comun', `SELECT id_template_menu FROM ${T} WHERE nombre = '${NOMBRE}'`);
  expect(idPlantilla, 'la plantilla está en la BD').toMatch(/^\d+$/);
  expect(Number(sqlDev('comun', `SELECT count(*) FROM public.ref_menus WHERE id_template_menu = ${idPlantilla}`)), 'con sus menús').toBeGreaterThan(0);
  expect(dialogos.join(' / '), 'avisa de que se han exportado').not.toBe('');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
