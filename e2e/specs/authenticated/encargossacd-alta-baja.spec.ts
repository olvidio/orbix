import { expect, test, type Locator } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de encargos de sacd (fase 5.3), por la interfaz:
 * buscar encargos → «añadir encargo» → grupo y tipo de encargo (desplegables encadenados) →
 * descripción → guardar (`encargo_ver_nuevo`) → vuelve a la búsqueda → buscarlo → marcarlo →
 * modificar → cambiar el lugar → guardar (`encargo_ver_editar`) → marcarlo → borrar con confirmación →
 * ya no está.
 *
 * Al final se borra con SQL cualquier encargo con la marca del test.
 */
const ESQUEMA = (process.env.E2E_ESQUEMA ?? 'H-dlbv').replace(/v$/, '');
const DESC = marcaE2e() + ' encargo';
const encargo = (campos: string) => sqlDev('comun', `SELECT ${campos} FROM "${ESQUEMA}".encargos WHERE desc_enc = '${DESC}'`);
/** Valores no vacíos de las opciones de un desplegable. */
const opciones = (select: Locator) => select.locator('option').evaluateAll((os) => os.map((o) => (o as HTMLOptionElement).value).filter((v) => v !== ''));

test.afterAll(() => {
  limpiar(() => sqlDev('comun', `DELETE FROM "${ESQUEMA}".encargos WHERE desc_enc = '${DESC}'`));
});

test('encargo: alta, modificación y borrado desde la lista', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');
  const form = main.locator('form#modifica');
  const buscarElDelTest = async () => {
    const buscar = main.locator('form#frm_encargo_que');
    await expect(buscar).toBeVisible({ timeout: 20_000 });
    await buscar.locator('[name="desc_enc"]').fill(DESC);
    await buscar.locator('#ok').click();
    const casilla = main.locator('form#seleccionados input.sel');
    await expect(casilla).toHaveCount(1, { timeout: 20_000 });
    await sinErrorDeBackend(page);
    await casilla.click({ force: true });
  };

  // Los encargos que hay antes de empezar: al final tienen que seguir todos y ser los mismos (el alta
  // llegó a sobrescribir uno que existía, con la secuencia de ids por detrás de los ids reales).
  const encargosAntes = sqlDev('comun', `SELECT md5(string_agg(id_enc || ':' || desc_enc, '|' ORDER BY id_enc)) FROM "${ESQUEMA}".encargos`);

  // --- Alta
  await abrirPantalla(page, 'frontend/encargossacd/controller/encargo_select.php', 'form#frm_encargo_que');
  // El enlace «añadir encargo» (los de la lista abren encargos que ya existen: no tocarlos).
  await main.locator('span.cabecera[onclick*="que=nuevo"]').click();
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await expect(form.locator('input[name="que"]'), 'la ficha es de alta').toHaveValue('nuevo');
  const grupo = (await opciones(form.locator('#grupo')))[0];
  expect(grupo, 'hay algún grupo de encargos').toBeTruthy();
  await Promise.all([
    page.waitForResponse((r) => r.url().includes('encargo_lst_tipo_enc'), { timeout: 20_000 }),
    form.locator('#grupo').selectOption(grupo),
  ]);
  await page.waitForLoadState('networkidle');
  const tipos = form.locator('#lst_tipo_enc select');
  await tipos.selectOption((await opciones(tipos))[0]);
  await form.locator('#desc_enc').fill(DESC);
  const alta = await esperarSrc(page, 'encargo_ver_nuevo', () => form.locator('[onclick*="fnjs_guardar("]').first().click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  const idEnc = encargo('id_enc');
  expect(idEnc, 'el encargo está en la BD').toMatch(/^\d+$/);

  // --- Modificación desde la lista
  await buscarElDelTest();
  await main.locator('form#seleccionados [onclick*="fnjs_modificar"]').first().click();
  await expect(form.locator('#desc_enc')).toHaveValue(DESC, { timeout: 20_000 });
  await expect(form.locator('input[name="id_enc"]'), 'la ficha es la del encargo del test').toHaveValue(idEnc);
  await sinErrorDeBackend(page);
  await form.locator('[name="desc_lugar"]').fill('lugar e2e');
  const cambio = await esperarSrc(page, 'encargo_ver_editar', () => form.locator('[onclick*="fnjs_guardar("]').first().click());
  expect(cambio.success, `${cambio.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(encargo('desc_lugar')).toBe('lugar e2e');

  // --- Borrado desde la lista
  await buscarElDelTest();
  const antes = dialogos.length;
  const borrado = await esperarSrc(page, 'encargo_ver_eliminar', () => main.locator('form#seleccionados [onclick*="fnjs_borrar"]').first().click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(antes);
  expect(encargo('id_enc'), 'ya no está en la BD').toBe('');

  expect(sqlDev('comun', `SELECT md5(string_agg(id_enc || ':' || desc_enc, '|' ORDER BY id_enc)) FROM "${ESQUEMA}".encargos`), 'los demás encargos no han cambiado').toBe(encargosAntes);

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
