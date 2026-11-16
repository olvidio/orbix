import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, crearNumerario, esperarSrc, limpiar, marcaE2e, marcarFila, redEnCalma, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Pantalla genérica de tablas dentro de un dossier (`DatosTablaRepo` / `tablaDB_update`), con el
 * dossier 1024 «congresos a los que ha asistido una persona»: «nuevo» → formulario → guardar →
 * sale en la lista → marcar → «modificar» → guardar → marcar → «eliminar» con confirmación.
 *
 * La persona es del propio test; al final se borran con SQL sus congresos, sus marcas de dossier y
 * la persona.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const T = `"${ESQUEMA_V}"`;
const APELLIDO = marcaE2e();
let idNom = '';

// Una opción que no está en un desplegable falla a los 20 s, no al agotar el tiempo del proyecto.
test.use({ actionTimeout: 20_000 });

const congresos = (campos: string) => sqlDev('sv', `SELECT coalesce(string_agg(${campos}, ';'), '') FROM ${T}.d_congresos WHERE id_nom = ${idNom || 0}`);
const personas = `(SELECT id_nom FROM ${T}.p_numerarios WHERE apellido1 = '${APELLIDO}')`;

test.afterAll(() => {
  limpiar(
    () => sqlDev('sv', `DELETE FROM ${T}.d_congresos WHERE id_nom IN ${personas}`),
    () => sqlDev('sv', `DELETE FROM ${T}.d_dossiers_abiertos WHERE tabla = 'p' AND id_pau IN ${personas}`),
    () => sqlDev('sv', `DELETE FROM ${T}.p_numerarios WHERE apellido1 = '${APELLIDO}'`),
  );
});

test('dossier con tabla genérica (congresos): alta, modificación y borrado', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  idNom = await crearNumerario(page, APELLIDO, ESQUEMA_V);
  const dossier = `frontend/dossiers/controller/dossiers_ver.php?pau=p&id_pau=${idNom}&obj_pau=PersonaN&id_dossier=1024&permiso=3`;
  const main = page.locator('#main');
  const form = main.locator('form').filter({ has: page.locator('[name="congreso"]') });

  // --- Alta.
  await abrirPantalla(page, dossier, 'input[name="btn_new"]');
  await main.locator('input[name="btn_new"]').click();
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await form.locator('[name="congreso"]').fill('Congreso de prueba e2e');
  await form.locator('[name="lugar"]').fill('Barcelona');
  await form.locator('#tipo').selectOption('2');
  const alta = await esperarSrc(page, 'tablaDB_update', () => form.locator('input[onclick*="fnjs_grabar"]').click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(congresos(`congreso || '|' || lugar || '|' || tipo`), 'el congreso está en la BD').toBe('Congreso de prueba e2e|Barcelona|2');
  await redEnCalma(page);

  // --- Modificación desde la lista.
  await abrirPantalla(page, dossier, 'input.sel', true);
  await expect(main.locator('input.sel'), 'sale en la lista').toHaveCount(1);
  await marcarFila(page, main.locator('input.sel').first());
  await main.locator('[onclick*="fnjs_modificar(this.form)"]').first().click();
  await expect(form.locator('[name="congreso"]')).toHaveValue('Congreso de prueba e2e', { timeout: 20_000 });
  await form.locator('[name="lugar"]').fill('Girona');
  const cambio = await esperarSrc(page, 'tablaDB_update', () => form.locator('input[onclick*="fnjs_grabar"]').click());
  expect(cambio.success, `${cambio.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(congresos(`congreso || '|' || lugar`), 'el lugar ha cambiado').toBe('Congreso de prueba e2e|Girona');
  await redEnCalma(page);

  // --- Borrado, con confirmación.
  await abrirPantalla(page, dossier, 'input.sel', true);
  await marcarFila(page, main.locator('input.sel').first());
  const avisosAntes = dialogos.length;
  const borrado = await esperarSrc(page, 'tablaDB_update', () => main.locator('[onclick*="fnjs_eliminar(this.form)"]').first().click());
  expect(borrado.success, `${borrado.mensaje ?? ''} ${dialogos.slice(avisosAntes).join(' / ')}`).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(avisosAntes);
  expect(congresos('congreso'), 'ya no está en la BD').toBe('');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
});
