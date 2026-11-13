import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de ubis (fase 5.3), por la interfaz, con una casa de la dl:
 * buscar casas de la dl por un nombre que no existe → «nuevo centro o casa» → ficha → guardar
 * (`ubis_update.php` → `/src/ubis/ubis_guardar`) → vuelve a la tabla, con la casa → «modificar» → ficha →
 * cambiar las plazas → guardar → de nuevo a la tabla → ficha → eliminar con confirmación
 * (`ubis_eliminar.php` → `/src/ubis/ubis_eliminar`) → ya no está y vuelve a la búsqueda.
 *
 * Al final se borra con SQL cualquier casa con la marca del test.
 */
const ESQUEMA = process.env.E2E_ESQUEMA_COMUN ?? 'H-dlb';
const NOMBRE = marcaE2e() + ' casa';
const casa = (campos: string) => sqlDev('comun', `SELECT ${campos} FROM "${ESQUEMA}".u_cdc_dl WHERE nombre_ubi = '${NOMBRE}'`);

test.afterAll(() => {
  limpiar(() => sqlDev('comun', `DELETE FROM "${ESQUEMA}".u_cdc_dl WHERE nombre_ubi = '${NOMBRE}'`));
});

test('casa de la dl: alta, modificación y borrado', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');
  const form = main.locator('form#frm2');
  const guardar = form.locator('th[onclick*="fnjs_guardar"]');

  // --- Búsqueda de casas de la dl por un nombre que no existe → ficha nueva
  await abrirPantalla(page, 'frontend/ubis/controller/ubis_buscar.php?simple=2', 'form#frm_buscar_2');
  const buscar = main.locator('form#frm_buscar_2');
  for (const [campo, valor] of [['#tipo', 'cdc'], ['#loc', 'dl']]) {
    // Cada desplegable recarga el formulario de condiciones.
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('ubis_buscar.php'), { timeout: 20_000 }),
      buscar.locator(campo).selectOption(valor),
    ]);
    await page.waitForLoadState('networkidle');
  }
  await buscar.locator('#nombre_ubi').fill(NOMBRE);
  await buscar.locator('#b_buscar_2').click();
  // (La búsqueda tiene otro botón con el mismo id: esperar a la tabla.)
  await expect(main.locator('form#seleccionados')).toBeAttached({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  const nuevo = main.locator('input#b_mas[onclick*="ubis_editar.php"]');
  await expect(nuevo).toBeVisible();
  await nuevo.click();
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await expect(form.locator('#tipo_ubi')).toHaveValue('cdcdl');
  await form.locator('[name="nombre_ubi"]').fill(NOMBRE);
  await form.locator('[name="plazas"]').fill('10');
  const alta = await esperarSrc(page, 'ubis_update', () => guardar.click());
  expect(alta.success, alta.mensaje).toBe(true);
  const idUbi = casa('id_ubi');
  expect(idUbi, 'la casa está en la BD').toMatch(/^-?\d+$/);
  expect(casa('plazas')).toBe('10');

  // --- Tras guardar se vuelve a la tabla, que ahora tiene la casa: marcarla → «modificar» → ficha
  const abrirFicha = async () => {
    const casilla = main.locator('#seleccionados input.sel');
    await expect(casilla).toHaveCount(1, { timeout: 20_000 });
    await casilla.click({ force: true });
    await main.locator('#seleccionados [onclick*="fnjs_modificar"]').first().click();
    const editar = main.locator('span.link_inv[onclick*="ubis_editar.php"]');
    await expect(editar).toBeVisible({ timeout: 20_000 });
    await sinErrorDeBackend(page);
    await editar.click();
    await expect(form.locator('[name="nombre_ubi"]')).toHaveValue(NOMBRE, { timeout: 20_000 });
  };
  await abrirFicha();
  await form.locator('[name="plazas"]').fill('12');
  const cambio = await esperarSrc(page, 'ubis_update', () => guardar.click());
  expect(cambio.success, cambio.mensaje).toBe(true);
  expect(casa('plazas')).toBe('12');

  // --- De nuevo en la tabla: ficha → eliminar → vuelve a la búsqueda
  await abrirFicha();
  const borrado = await esperarSrc(page, 'ubis_eliminar', () => form.locator('th[onclick*="fnjs_eliminar"]').click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(casa('id_ubi'), 'ya no está en la BD').toBe('');
  await expect(main.locator('form#frm_buscar_1, form#frm_buscar_2').first()).toBeAttached({ timeout: 20_000 });

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
