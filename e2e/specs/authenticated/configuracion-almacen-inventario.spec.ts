import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Parámetro de configuración `inventario_ubi_almacen` (centro de inventario que hace de almacén), por
 * la interfaz: parámetros → elegir otro centro con lugares → guardar (`parametros_update`) → la BD lo
 * recoge y la pantalla lo muestra elegido al recargar.
 *
 * El valor que había (el almacén real del esquema de dev, del que depende `inventario-equipajes`) se
 * repone al final con SQL.
 */
const ESQUEMA = process.env.E2E_ESQUEMA_COMUN ?? 'H-dlb';
const TABLA = `"${ESQUEMA}".x_config_schema`;
const PARAMETRO = 'inventario_ubi_almacen';

const sql = (consulta: string) => sqlDev('comun', consulta);
const valorEnBd = () => sql(`SELECT valor FROM ${TABLA} WHERE parametro = '${PARAMETRO}'`);
const original = valorEnBd();

test.afterAll(() => {
  limpiar(() =>
    original === ''
      ? sql(`DELETE FROM ${TABLA} WHERE parametro = '${PARAMETRO}'`)
      : sql(`UPDATE ${TABLA} SET valor = '${original}' WHERE parametro = '${PARAMETRO}'`),
  );
});

test('parámetros: elegir el centro de inventario que hace de almacén', async ({ page }) => {
  await abrirApp(page);
  aceptarDialogos(page);
  const main = page.locator('#main');

  await abrirPantalla(page, 'frontend/configuracion/controller/parametros.php', 'form#frm_almacen_inventario');
  await sinErrorDeBackend(page);
  const almacen = main.locator('#frm_almacen_inventario select[name="valor"]');
  await expect(almacen, 'muestra el almacén definido').toHaveValue(original);
  const otros = await almacen.locator('option').evaluateAll(
    (os, actual) => os.map((o) => (o as HTMLOptionElement).value).filter((v) => v !== '' && v !== actual),
    original,
  );
  expect(otros.length, 'hay otro centro con lugares para elegir').toBeGreaterThan(0);
  const nuevo = otros[0]!;

  await almacen.selectOption(nuevo);
  const guardado = await esperarSrc(page, 'parametros_update', () => main.locator('#frm_almacen_inventario input[onclick*="fnjs_guardar"]').click());
  expect(guardado.success, guardado.mensaje).toBe(true);
  expect(valorEnBd(), 'el parámetro está en la BD').toBe(nuevo);

  await abrirPantalla(page, 'frontend/configuracion/controller/parametros.php', 'form#frm_almacen_inventario');
  await expect(main.locator('#frm_almacen_inventario select[name="valor"]'), 'al recargar sale elegido').toHaveValue(nuevo);
});
