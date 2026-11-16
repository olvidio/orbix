import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, redEnCalma, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Dossier 3010 «centros encargados de la actividad» (el que abre «ctrs org» en la lista de
 * actividades sg): es de solo consulta. Lista los centros de la actividad por su nombre y no ofrece
 * «nuevo», «modificar» ni «eliminar»: los centros se asignan en su pantalla propia (duda 26 del plan
 * de revisión de tests). Con una actividad que ya tenía centros daba un error fatal.
 *
 * Solo lectura: usa una actividad real de la dl con al menos dos centros de sv.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const ESQUEMA = ESQUEMA_V.replace(/v$/, '');

test('dossier 3010: lista los centros encargados de la actividad, sin botones de edición', async ({ page }) => {
  const idActiv = sqlDev(
    'comun',
    `SELECT e.id_activ FROM "${ESQUEMA}".da_ctr_encargados e JOIN "${ESQUEMA}".a_actividades_dl a USING (id_activ)
      GROUP BY e.id_activ, a.f_ini HAVING count(*) >= 2 AND bool_and(e.id_ubi::text LIKE '1%') ORDER BY a.f_ini DESC, e.id_activ LIMIT 1`,
  );
  test.skip(idActiv === '', 'no hay ninguna actividad de la dl con dos centros encargados de sv');
  const idsUbi = sqlDev('comun', `SELECT string_agg(id_ubi::text, ',') FROM "${ESQUEMA}".da_ctr_encargados WHERE id_activ = ${idActiv}`);
  const nombres = sqlDev('sv', `SELECT string_agg(nombre_ubi, '|') FROM "${ESQUEMA_V}".u_centros_dl WHERE id_ubi IN (${idsUbi})`).split('|').filter((n) => n !== '');
  expect(nombres.length, 'los centros de la actividad están en la tabla de centros de la dl').toBeGreaterThanOrEqual(2);

  const inc = await abrirApp(page);
  const main = page.locator('#main');
  await abrirPantalla(page, `frontend/dossiers/controller/dossiers_ver.php?pau=a&id_pau=${idActiv}&obj_pau=Actividad&id_dossier=3010&permiso=3`, '#ficha3010');
  await redEnCalma(page);
  await sinErrorDeBackend(page);
  await expect(main).not.toContainText('Call to undefined method');
  for (const nombre of nombres) {
    await expect(main.locator('#ficha3010'), `sale el centro «${nombre}»`).toContainText(nombre);
  }
  await expect(main.locator('#ficha3010 input[name="btn_new"]'), 'no se ofrece «nuevo»').toHaveCount(0);
  await expect(main.locator('#ficha3010 [onclick*="fnjs_modificar(this.form)"]'), 'ni «modificar»').toHaveCount(0);
  await expect(main.locator('#ficha3010 [onclick*="fnjs_eliminar(this.form)"]'), 'ni «eliminar»').toHaveCount(0);
  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
});
