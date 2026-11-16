import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, limpiar, marcaE2e, redEnCalma, sqlDev } from '../../orbix-app';

/**
 * Gestión de tipos de actividad (`frontend/actividades/controller/tipo_activ.php`): la lista, el alta con los
 * desplegables del tipo, el cambio de nombre y el borrado, por la interfaz.
 *
 * Los formularios llevan campos firmados (`HashF`) y cápsulas `ctx_*` (`HashB`): si no coinciden, guardar se
 * rechaza. El tipo se crea con un código libre (`…99x`) y se borra al final (también con SQL).
 */
const ESQUEMA = (process.env.E2E_ESQUEMA ?? 'H-dlbv').replace(/v$/, '');
const T = `"${ESQUEMA}".a_tipos_actividad`;
let idTipo = '';

test.afterAll(() => {
  limpiar(() => {
    if (/^\d{6}$/.test(idTipo)) {
      sqlDev('comun', `DELETE FROM ${T} WHERE id_tipo_activ = ${idTipo}`);
    }
  });
});

test('tipos de actividad: lista, alta, cambio de nombre y borrado', async ({ page }) => {
  await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');
  const nombre = (id: string) => sqlDev('comun', `SELECT coalesce((SELECT nombre FROM ${T} WHERE id_tipo_activ = ${id}), '-')`);

  await abrirPantalla(page, 'frontend/actividades/controller/tipo_activ.php', '#div_tabla span.link[id^="mod_"]');
  const hay = Number(sqlDev('comun', `SELECT count(*) FROM ${T}`));
  await expect(main.locator('#div_tabla span.link[id^="mod_"]')).toHaveCount(hay);

  // Alta: sv, y los primeros asistentes y actividad con valor.
  await page.evaluate(() => {
    // @ts-expect-error función global de la vista
    fnjs_nuevo();
  });
  const form = main.locator('#div_modificar form#frm_tipo_activ');
  await expect(form.locator('#id_nom_tipo_activ')).toBeVisible({ timeout: 20_000 });
  await redEnCalma(page);
  const primera = async (sel: string) => {
    const v = await main
      .locator(`#div_modificar ${sel} option`)
      .evaluateAll((os) => os.map((o) => (o as HTMLOptionElement).value).find((x) => /^\d+$/.test(x)) ?? '');
    expect(v, sel).not.toBe('');
    await main.locator(`#div_modificar ${sel}`).selectOption(v);
    await redEnCalma(page);
    return v;
  };
  const prefijo = (await primera('#isfsv_val')) + (await primera('#iasistentes_val')) + (await primera('#iactividad_val'));
  // Al elegir la actividad sale la tabla de los tipos que ya existen con ese prefijo (código y nombre).
  const existentes = Number(sqlDev('comun', `SELECT count(*) FROM ${T} WHERE id_tipo_activ::text LIKE '${prefijo}%'`));
  const tabla = main.locator('#div_modificar #lst_nom_tipo table.sortable');
  await expect(tabla).toBeVisible({ timeout: 20_000 });
  await expect(tabla.locator('tbody tr')).toHaveCount(existentes);
  // Con los desplegables extendidos la actividad tiene dos cifras y el código del tipo, dos.
  expect(prefijo).toMatch(/^\d{3,4}$/);
  const resto = 6 - prefijo.length;
  const base = Number(prefijo) * 10 ** resto;
  idTipo = sqlDev(
    'comun',
    `SELECT x FROM generate_series(${base + 10 ** resto - 10}, ${base + 10 ** resto - 1}) x WHERE NOT EXISTS (SELECT 1 FROM ${T} WHERE id_tipo_activ = x) LIMIT 1`,
  );
  expect(idTipo).toMatch(/^\d{6}$/);
  const marca = marcaE2e();
  await form.locator('#id_nom_tipo_activ').fill(idTipo.slice(prefijo.length));
  await form.locator('#nom_tipo_activ').fill(`e2e ${marca}`);
  await form.locator('input[type="button"][onclick*="fnjs_guardar_nuevo"]').click();
  await expect.poll(() => nombre(idTipo), { timeout: 20_000 }).toBe(`e2e ${marca}`);

  // Cambio de nombre desde «modificar».
  const enlace = main.locator(`#div_tabla #mod_${idTipo}`);
  // Tras guardar, la lista se recarga y su respuesta cierra la ventana: esperar a que termine antes de abrir otra.
  await redEnCalma(page);
  await expect(enlace).toBeVisible({ timeout: 20_000 });
  await enlace.click();
  await expect(form.locator('#nom_tipo_activ')).toHaveValue(`e2e ${marca}`, { timeout: 20_000 });
  await form.locator('#nom_tipo_activ').fill(`e2e ${marca} bis`);
  await form.locator('input[type="button"][onclick*="\'update\'"]').click();
  await expect.poll(() => nombre(idTipo), { timeout: 20_000 }).toBe(`e2e ${marca} bis`);

  // Borrado.
  // Tras guardar, la lista se recarga y su respuesta cierra la ventana: esperar a que termine antes de abrir otra.
  await redEnCalma(page);
  await expect(enlace).toBeVisible({ timeout: 20_000 });
  await enlace.click();
  await expect(form.locator('#nom_tipo_activ')).toHaveValue(`e2e ${marca} bis`, { timeout: 20_000 });
  await form.locator('input[type="button"][onclick*="\'eliminar\'"]').click();
  await expect.poll(() => nombre(idTipo), { timeout: 20_000 }).toBe('-');
  await expect(main.locator(`#div_tabla #mod_${idTipo}`)).toHaveCount(0, { timeout: 20_000 });

  expect(dialogos.filter((d) => /error|no se ha|incorrecto/i.test(d))).toEqual([]);
});
