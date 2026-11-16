import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, redEnCalma, sqlDev } from '../../orbix-app';

/**
 * Relación tarifa ↔ tipo de actividad (fase 6.4): alta desde «nueva relación tarifa-tipo actividad»
 * y borrado desde su ficha, por la interfaz.
 *
 * El formulario de alta envía los desplegables del tipo de actividad (con su campo oculto `extendida`)
 * y rellena por JavaScript el oculto `id_tipo_activ`: si el hash del formulario no lo tiene en cuenta,
 * «guardar» se rechaza sin mensaje (302 al inicio y sesión cerrada).
 *
 * La relación se crea para un tipo de actividad que no tiene ninguna y se borra al final (también con
 * SQL, por si el test falla a medias).
 */
const ESQUEMA = (process.env.E2E_ESQUEMA ?? 'H-dlbv').replace(/v$/, '');
const T = `"${ESQUEMA}".xa_tipo_activ_tarifa`;
let idTipoActiv = '';

test.afterAll(() => {
  limpiar(() => {
    if (/^\d+$/.test(idTipoActiv)) {
      sqlDev('comun', `DELETE FROM ${T} WHERE id_tipo_activ = ${idTipoActiv} AND id_item > ${maxAntes}`);
    }
  });
});

let maxAntes = '0';

test('relación tarifa–tipo de actividad: alta y borrado', async ({ page }) => {
  maxAntes = sqlDev('comun', `SELECT coalesce(max(id_item), 0) FROM ${T}`);
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');

  await abrirPantalla(page, 'frontend/actividadtarifas/controller/tarifa_tipo_actividad.php', '#ficha span.link[onclick*="nuevo"]');
  await main.locator('#ficha span.link[onclick*="nuevo"]').click();
  const alta = main.locator('#div_modificar form#nuevo');
  await expect(alta).toBeVisible({ timeout: 20_000 });
  await redEnCalma(page);

  // Un tipo de actividad concreto (asistentes + actividad + tipo) que todavía no tenga tarifa.
  // Los desplegables se cargan en cascada; «...» (cualquiera) no vale para guardar.
  const opciones = (sel: string) =>
    alta.locator(`${sel} option`).evaluateAll((os) => os.map((o) => (o as HTMLOptionElement).value).filter((v) => /^\d+$/.test(v)));
  let elegido: string[] = [];
  buscar: for (const asistentes of await opciones('#iasistentes_val')) {
    await alta.locator('#iasistentes_val').selectOption(asistentes);
    await redEnCalma(page);
    for (const actividad of await opciones('#iactividad_val')) {
      await alta.locator('#iactividad_val').selectOption(actividad);
      await redEnCalma(page);
      for (const tipo of await opciones('#inom_tipo_val')) {
        const id = await page.evaluate(
          ([a, b, c]) => {
            const valor = (sel: string) => String((document.querySelector(sel) as HTMLInputElement).value);
            // @ts-expect-error función global de la vista
            return String(fnjs_id_tipo_activ_from_parts(valor('#isfsv_val'), a, b, c, valor('#extendida')));
          },
          [asistentes, actividad, tipo],
        );
        if (/^\d+$/.test(id) && sqlDev('comun', `SELECT count(*) FROM ${T} WHERE id_tipo_activ = ${id}`) === '0') {
          await alta.locator('#inom_tipo_val').selectOption(tipo);
          idTipoActiv = id;
          elegido = [asistentes, actividad, tipo];
          break buscar;
        }
      }
    }
  }
  expect(idTipoActiv, 'hay algún tipo de actividad sin tarifa').toMatch(/^\d+$/);
  const tarifa = (await opciones('#id_tarifa'))[0];
  expect(tarifa, 'hay alguna tarifa').toBeTruthy();
  await alta.locator('#id_tarifa').selectOption(tarifa);

  // Sin tipo concreto («...» en el tercer desplegable) no se guarda: no hay tarifas genéricas para
  // «asistentes + actividad».
  await alta.locator('#inom_tipo_val').selectOption('...');
  const sinTipo = await esperarSrc(page, 'relacion_tarifa_update', () => alta.locator('input[onclick*="fnjs_guardar"]').click());
  expect(sinTipo.success, 'con «...» no se guarda').toBe(false);
  expect(sinTipo.mensaje, 'explica por qué').toBeTruthy();
  expect(sqlDev('comun', `SELECT count(*) FROM ${T} WHERE id_item > ${maxAntes}`), 'no se ha creado ninguna relación').toBe('0');
  await alta.locator('#inom_tipo_val').selectOption(elegido[2]);

  const guardado =await esperarSrc(page, 'relacion_tarifa_update', () => alta.locator('input[onclick*="fnjs_guardar"]').click());
  expect(guardado.success, guardado.mensaje).toBe(true);
  const idItem = sqlDev('comun', `SELECT id_item FROM ${T} WHERE id_tipo_activ = ${idTipoActiv} AND id_item > ${maxAntes}`);
  expect(idItem, 'la relación está en la BD').toMatch(/^\d+$/);
  expect(sqlDev('comun', `SELECT id_tarifa FROM ${T} WHERE id_item = ${idItem}`)).toBe(tarifa);

  // Otra alta con el mismo tipo de actividad: (id_tipo_activ, id_serie) es UNIQUE. Tiene que responder
  // JSON con un aviso (antes el INSERT lanzaba una excepción y el JS decía «error de comunicación»).
  await redEnCalma(page);
  await main.locator('#ficha span.link[onclick*="nuevo"]').click();
  await expect(alta).toBeVisible({ timeout: 20_000 });
  await redEnCalma(page);
  for (const [sel, valor] of [['#iasistentes_val', elegido[0]], ['#iactividad_val', elegido[1]], ['#inom_tipo_val', elegido[2]]]) {
    await alta.locator(sel).selectOption(valor);
    await redEnCalma(page);
  }
  await alta.locator('#id_tarifa').selectOption(tarifa);
  const avisosAntes = dialogos.length;
  const repetido = await esperarSrc(page, 'relacion_tarifa_update', () => alta.locator('input[onclick*="fnjs_guardar"]').click());
  expect(repetido.success, 'no deja dos tarifas para el mismo tipo').toBe(false);
  expect(repetido.mensaje, 'explica por qué').toBeTruthy();
  await expect.poll(() => dialogos.slice(avisosAntes).join(' / ')).toContain(String(repetido.mensaje));
  expect(sqlDev('comun', `SELECT count(*) FROM ${T} WHERE id_tipo_activ = ${idTipoActiv}`)).toBe('1');
  await main.locator('#div_modificar input[onclick*="fnjs_cerrar"]').click();

  // Borrarla desde su ficha (la lista se recarga tras guardar).
  await redEnCalma(page);
  await page.evaluate((id) => {
    // @ts-expect-error función global de la vista
    fnjs_modificar(id);
  }, idItem);
  const ficha = main.locator('#div_modificar form#modifica');
  await expect(ficha).toBeVisible({ timeout: 20_000 });
  const borrado = await esperarSrc(page, 'relacion_tarifa_eliminar', () => ficha.locator('input[onclick*="eliminar"]').click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(sqlDev('comun', `SELECT count(*) FROM ${T} WHERE id_item = ${idItem}`), 'ya no está en la BD').toBe('0');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
