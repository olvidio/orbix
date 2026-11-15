import { expect, test, type Locator, type Page } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, redEnCalma, sqlDev } from '../../orbix-app';

/**
 * Parámetros de la pasarela (los formularios solo tenían comprobado el hash en `formularios-de-alta`),
 * con guardado real por la interfaz:
 *
 * - Contribución de quien no duerme y contribución de reserva: «nuevo» → tipo de actividad
 *   (desplegables encadenados) y valor → guardar (`contribucion_*_excepcion_guardar`) → sale en la
 *   lista → abrirla → modificar → abrirla → eliminar con confirmación → cambiar el valor por defecto.
 * - Nombres: «nuevo» → tipo de actividad y nombre → guardar (`nombre_excepcion_guardar`).
 *
 * Se guarda una foto de la fila de cada parámetro en la dl y se repone al final.
 */
const ESQUEMA = (process.env.E2E_ESQUEMA ?? 'H-dlbv').replace(/v$/, '');
const TABLA = `"${ESQUEMA}".pasarela_dl`;
const TIPO_SV_AGD_CRT = '131000';
const fotos = new Map<string, string>();

// Una opción que no está en un desplegable falla a los 20 s, no al agotar el tiempo del proyecto.
test.use({ actionTimeout: 20_000 });

const valor = (parametro: string, ruta: string) => sqlDev('comun', `SELECT coalesce(json_valor #>> '{${ruta}}', '') FROM ${TABLA} WHERE nom_parametro = '${parametro}'`);

/** Foto de la fila del parámetro, para reponerla al final. Devuelve si ya tiene una excepción para el tipo del test. */
function fotografiar(parametro: string): boolean {
  const foto = sqlDev('comun', `SELECT coalesce(json_agg(t), '[]') FROM ${TABLA} t WHERE nom_parametro = '${parametro}'`);
  fotos.set(parametro, foto);
  return foto.includes(`"${TIPO_SV_AGD_CRT}"`);
}

/** Elige en el formulario de alta el tipo de actividad sv · agd · crt · (sin especificar). */
async function elegirTipo(page: Page, nuevo: Locator): Promise<void> {
  for (const [campo, opcion] of [['#iasistentes_val', '3'], ['#iactividad_val', '1'], ['#inom_tipo_val', '000']]) {
    await nuevo.locator(campo).selectOption(opcion);
    await redEnCalma(page);
  }
}

test.afterAll(() => {
  for (const [parametro, foto] of fotos) {
    sqlDev('comun', `DELETE FROM ${TABLA} WHERE nom_parametro = '${parametro}'`);
    sqlDev('comun', `INSERT INTO ${TABLA} SELECT * FROM json_populate_recordset(NULL::${TABLA}, '${foto.replace(/'/g, "''")}')`);
  }
});

for (const PARAMETRO of ['contribucion_no_duerme', 'contribucion_reserva']) {
  test(`pasarela, ${PARAMETRO}: excepción (alta, modificación y borrado) y valor por defecto`, async ({ page }) => {
    test.skip(fotografiar(PARAMETRO), 'la dl ya tiene una excepción para sv · agd · crt');

    const inc = await abrirApp(page);
    const dialogos = aceptarDialogos(page);
    const main = page.locator('#main');
    await abrirPantalla(page, `frontend/pasarela/controller/${PARAMETRO}_lista.php`, '#div_tabla table');
    await redEnCalma(page);
    const fila = main.locator(`#div_tabla [onclick^="fnjs_modificar(${TIPO_SV_AGD_CRT},"]`);
    const porDefecto = main.locator('#div_tabla [onclick="fnjs_modificar_default()"]');
    const defectoAntes = (await porDefecto.innerText()).trim();

    // --- Alta: tipo de actividad sv · agd · crt · (sin especificar), valor 40.
    await main.locator('#div_botones input[onclick*="fnjs_nuevo"]').click();
    const nuevo = main.locator('#div_modificar form#nuevo');
    await expect(nuevo).toBeVisible({ timeout: 20_000 });
    await elegirTipo(page, nuevo);
    await nuevo.locator('#contribucion').fill('40');
    const alta = await esperarSrc(page, `${PARAMETRO}_excepcion_guardar`, () => nuevo.locator('input[onclick*="fnjs_guardar_nuevo"]').click());
    expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
    expect(valor(PARAMETRO, `excepciones,${TIPO_SV_AGD_CRT}`), 'la excepción está en la BD').toBe('40');
    await redEnCalma(page);
    await expect(fila, 'sale en la lista').toHaveText('40');

    // --- Modificación: 55.
    await fila.click();
    const modifica = main.locator('#div_modificar form#modifica');
    await expect(modifica.locator('#contribucion')).toHaveValue('40', { timeout: 20_000 });
    await modifica.locator('#contribucion').fill('55');
    const cambio = await esperarSrc(page, `${PARAMETRO}_ajax`, () => modifica.locator(`input[onclick*="'update'"]`).click());
    expect(cambio.success, `${cambio.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
    expect(valor(PARAMETRO, `excepciones,${TIPO_SV_AGD_CRT}`), 'el valor ha cambiado').toBe('55');
    await redEnCalma(page);
    await expect(fila).toHaveText('55');

    // --- Borrado, con confirmación.
    const avisosAntes = dialogos.length;
    await fila.click();
    await expect(modifica.locator('#contribucion')).toHaveValue('55', { timeout: 20_000 });
    const borrado = await esperarSrc(page, `${PARAMETRO}_ajax`, () => modifica.locator(`input[onclick*="'eliminar'"]`).click());
    expect(borrado.success, `${borrado.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
    expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(avisosAntes);
    expect(valor(PARAMETRO, `excepciones,${TIPO_SV_AGD_CRT}`), 'ya no está en la BD').toBe('');
    await redEnCalma(page);
    await expect(fila, 'ni en la lista').toHaveCount(0);

    // --- Valor por defecto.
    const defectoNuevo = defectoAntes === '80' ? '81' : '80';
    await porDefecto.click();
    await expect(modifica.locator('#default')).toHaveValue(defectoAntes, { timeout: 20_000 });
    await modifica.locator('#default').fill(defectoNuevo);
    const defecto = await esperarSrc(page, `${PARAMETRO}_ajax`, () => modifica.locator('input[onclick*="fnjs_guardar_default"]').click());
    expect(defecto.success, `${defecto.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
    expect(valor(PARAMETRO, 'default'), 'el valor por defecto ha cambiado').toBe(defectoNuevo);
    await redEnCalma(page);
    await expect(porDefecto).toHaveText(defectoNuevo);

    expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  });
}

test('pasarela, nombre: alta de la excepción de un tipo de actividad', async ({ page }) => {
  test.skip(fotografiar('nombre'), 'la dl ya tiene un nombre para sv · agd · crt');

  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');
  await abrirPantalla(page, 'frontend/pasarela/controller/nombre_lista.php', '#div_botones');
  await redEnCalma(page);
  await main.locator('#div_botones input[onclick*="fnjs_nuevo"]').click();
  const nuevo = main.locator('#div_modificar form#nuevo');
  await expect(nuevo).toBeVisible({ timeout: 20_000 });
  await elegirTipo(page, nuevo);
  await nuevo.locator('[name="nombre_actividad"]').fill('Curso de retiro de prueba e2e');
  const alta = await esperarSrc(page, 'nombre_excepcion_guardar', () => nuevo.locator('input[onclick*="fnjs_guardar_nuevo"]').click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(valor('nombre', `excepciones,${TIPO_SV_AGD_CRT}`), 'el nombre está en la BD').toBe('Curso de retiro de prueba e2e');
  await redEnCalma(page);
  await expect(main.locator('#div_tabla'), 'sale en la lista').toContainText('Curso de retiro de prueba e2e');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
});
