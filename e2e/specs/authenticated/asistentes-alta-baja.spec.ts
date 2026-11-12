import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, borrarActividadSql, crearActividad, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de asistentes (fase 3.3), por la interfaz:
 * crear una actividad → abrir su dossier de asistentes (3101) → «+ n» → elegir un numerario →
 * guardar (`asistente_guardar`) → está en la BD → marcarlo en la lista → «borrar asistencia» con
 * confirmación (`asistente_eliminar`) → ya no está.
 *
 * La actividad es del propio test (año 2031); al final se borran con SQL la actividad, sus fases
 * y cualquier asistencia que haya quedado.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const ESQUEMA = ESQUEMA_V.replace(/v$/, '');
const DL = ESQUEMA.split('-')[1] ?? 'dlb';
const NOMBRE = `${marcaE2e()} asistentes`;
let idActiv = '';
// Apuntar y quitar a alguien toca las marcas de sus dossiers (d_dossiers_abiertos): se guarda una
// foto de las filas de esa persona antes y se reponen al final, para no dejar cambiado a nadie real.
let persona = '';
let fotoDossiers = '[]';
const DOSSIERS = `"${ESQUEMA_V}".d_dossiers_abiertos`;

const asistencias = (campos: string) => sqlDev('sv-e', `SELECT ${campos} FROM "${ESQUEMA_V}".d_asistentes_dl WHERE id_activ = ${idActiv || 0} ORDER BY id_nom`);

test.afterAll(() => {
  limpiar(
    () => {
      if (persona !== '') {
        sqlDev('sv', `DELETE FROM ${DOSSIERS} WHERE tabla = 'p' AND id_pau = ${persona}`);
        sqlDev('sv', `INSERT INTO ${DOSSIERS} SELECT * FROM json_populate_recordset(NULL::${DOSSIERS}, '${fotoDossiers.replace(/'/g, "''")}')`);
      }
    },
    () => {
      if (idActiv !== '') {
        sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".d_asistentes_dl WHERE id_activ = ${idActiv}`);
      }
    },
    () => borrarActividadSql(ESQUEMA, NOMBRE),
  );
});

test('asistente: apuntar a un numerario en una actividad y quitarlo', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  idActiv = await crearActividad(page, NOMBRE, DL, ESQUEMA);

  const dossier = `frontend/dossiers/controller/dossiers_ver.php?pau=a&id_pau=${idActiv}&obj_pau=Actividad&id_dossier=3101&permiso=3`;
  await abrirPantalla(page, dossier, '.links-insert-btn');
  await page.locator('#main .links-insert-btn').first().click();
  const form = page.locator('#main form').filter({ has: page.locator('select[name="id_nom"]') });
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);

  persona = await form.locator('select[name="id_nom"] option').evaluateAll((os) =>
    (os as HTMLOptionElement[]).map((o) => o.value).find((v) => /^\d+$/.test(v) && v !== '0') ?? '',
  );
  expect(persona, 'hay numerarios para elegir').not.toBe('');
  fotoDossiers = sqlDev('sv', `SELECT coalesce(json_agg(t), '[]') FROM ${DOSSIERS} t WHERE tabla = 'p' AND id_pau = ${persona}`);
  await form.locator('select[name="id_nom"]').selectOption(persona);
  await form.locator('[name="observ"]').fill('apuntado por e2e');
  const alta = await esperarSrc(page, 'asistente_guardar', () => form.locator('input[onclick*="fnjs_guardar(this.form,0)"]').click());
  expect(alta.success, alta.mensaje).toBe(true);
  expect(asistencias(`id_nom || '|' || observ || '|' || propio`)).toBe(`${persona}|apuntado por e2e|true`);

  // Tras guardar vuelve a la lista de asistentes del dossier.
  await abrirPantalla(page, dossier, 'input.sel', true);
  const casillas = page.locator('#main input.sel');
  await expect(casillas, 'el asistente sale en la lista').toHaveCount(1);
  await casillas.first().click({ force: true });
  const borrado = await esperarSrc(page, 'asistente_eliminar', () => page.locator('#main [onclick*="fnjs_borrar(this.form)"]').first().click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(asistencias('id_nom'), 'ya no está apuntado').toBe('');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
