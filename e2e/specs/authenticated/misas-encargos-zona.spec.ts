import { expect, test, type Locator } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de misas (fase 5.3), por la interfaz:
 * «encargos de la zona» (`modificar_encargos.php`) → la rejilla de la primera zona → «nuevo» →
 * nombre y tipo de encargo → grabar (`guardar_encargo_zona`) → está en la BD y en la rejilla →
 * clic en su fila → cambiar la descripción del lugar → grabar → clic en su fila → eliminar
 * (`eliminar_encargo_zona`) → ya no está.
 *
 * Al final se borra con SQL cualquier encargo con la marca del test, y se comprueba que los demás
 * encargos no han cambiado.
 */
const ESQUEMA = (process.env.E2E_ESQUEMA ?? 'H-dlbv').replace(/v$/, '');
const DESC = marcaE2e() + ' misa';
const encargo = (campos: string) => sqlDev('comun', `SELECT ${campos} FROM "${ESQUEMA}".encargos WHERE desc_enc = '${DESC}'`);
const huella = () => sqlDev('comun', `SELECT md5(string_agg(id_enc || ':' || desc_enc || ':' || coalesce(id_zona::text, ''), '|' ORDER BY id_enc)) FROM "${ESQUEMA}".encargos WHERE desc_enc <> '${DESC}'`);
/** Valores no vacíos de las opciones de un desplegable. */
const opciones = (select: Locator) => select.locator('option').evaluateAll((os) => os.map((o) => (o as HTMLOptionElement).value).filter((v) => v !== ''));

test.afterAll(() => {
  limpiar(() => sqlDev('comun', `DELETE FROM "${ESQUEMA}".encargos WHERE desc_enc = '${DESC}'`));
});

test('encargos de una zona: alta, modificación y borrado en la rejilla', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');
  const modal = main.locator('#modal_encargos_zona');
  const antes = huella();
  /** Graba (o elimina) desde la ventana y espera a que la rejilla se repinte. */
  const enviar = async (ruta: string, boton: string) => {
    const repintado = page.waitForResponse((r) => r.url().includes('ver_encargos_zona.php'), { timeout: 20_000 });
    const r = await esperarSrc(page, ruta, () => modal.locator(`button[data-action="${boton}"]`).click());
    await repintado;
    await page.waitForLoadState('networkidle');
    return r;
  };
  const abrirElDelTest = async () => {
    const celda = main.locator('#myGrid .slick-cell', { hasText: DESC }).first();
    await expect(celda).toBeVisible({ timeout: 20_000 });
    await celda.click();
    await expect(modal).toBeVisible();
    await expect(modal.locator('#encargo')).toHaveValue(DESC);
  };

  await abrirPantalla(page, 'frontend/misas/controller/modificar_encargos.php', '#div_encargos #myGrid');
  await sinErrorDeBackend(page);
  const idZona = await main.locator('#id_zona').inputValue();
  expect(idZona, 'hay alguna zona').toMatch(/^\d+$/);

  // --- Alta
  await main.locator('input[onclick*="fnjs_nuevo"]').click();
  await expect(modal).toBeVisible();
  await modal.locator('#encargo').fill(DESC);
  await modal.locator('#id_tipo_enc').selectOption((await opciones(modal.locator('#id_tipo_enc')))[0]);
  // Orden alto para que la fila quede al final y no mueva a las demás.
  await modal.locator('#orden').fill('9999');
  const alta = await enviar('guardar_encargo_zona', 'save');
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(encargo(`id_zona`), 'el encargo está en la BD, en la zona').toBe(idZona);

  // --- Modificación
  await abrirElDelTest();
  await modal.locator('#descripcion_lugar').fill('lugar e2e');
  const cambio = await enviar('guardar_encargo_zona', 'save');
  expect(cambio.success, `${cambio.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(encargo('desc_lugar')).toBe('lugar e2e');
  expect(encargo('count(*)'), 'se modifica el mismo, no se crea otro').toBe('1');

  // --- Borrado
  await abrirElDelTest();
  const borrado = await enviar('eliminar_encargo_zona', 'eliminar_encargo');
  expect(borrado.success, `${borrado.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(encargo('count(*)'), 'ya no está en la BD').toBe('0');

  expect(huella(), 'los demás encargos no han cambiado').toBe(antes);
  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
