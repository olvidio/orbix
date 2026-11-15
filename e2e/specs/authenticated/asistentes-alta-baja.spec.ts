import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, borrarActividadSql, crearActividad, esperarSrc, limpiar, marcaE2e, marcarFila, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de asistentes (fase 3.3), por la interfaz:
 * crear una actividad → abrir su dossier de asistentes (3101) → «+ n» → elegir un numerario →
 * guardar (`asistente_guardar`) → está en la BD → «modificar asistencia» desde la lista → asignar
 * plaza a los marcados (`asistente_plaza_asignar`, si hay módulo de plazas) → abrir el dossier de
 * actividades de la persona (1301) → marcar la actividad → «borrar asistencia» con confirmación
 * (`asistente_eliminar`) → ya no está. Cada acción viaja con la cápsula que firma su pantalla (fase 6.5).
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

const CAPSULA = /^[\w.%-]{20,}$/;

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

  // --- Modificar la asistencia desde la lista de la actividad (la cápsula fija actividad y persona).
  await marcarFila(page, casillas.first());
  await page.locator('#main [onclick*="fnjs_modificar(this.form)"]').first().click();
  const edicion = page.locator('#main form').filter({ has: page.locator('[name="observ"]') });
  await expect(edicion.locator('[name="observ"]')).toHaveValue('apuntado por e2e', { timeout: 20_000 });
  await edicion.locator('[name="observ"]').fill('modificado por e2e');
  const cambio = await esperarSrc(page, 'asistente_guardar', () => edicion.locator('input[onclick*="fnjs_guardar(this.form"]').first().click());
  expect(cambio.success, cambio.mensaje).toBe(true);
  expect(asistencias(`id_nom || '|' || observ`)).toBe(`${persona}|modificado por e2e`);

  // --- Asignar plaza a los marcados (si la dl tiene el módulo de plazas): la cápsula fija la actividad.
  await abrirPantalla(page, dossier, 'input.sel', true);
  const cajaPlaza = page.locator('#main [onclick*="fnjs_cmb_plaza"]');
  if ((await cajaPlaza.count()) > 0) {
    const plazaNueva = (await cajaPlaza.evaluateAll((cs) => cs.map((c) => /,'(\d+)'\)/.exec(c.getAttribute('onclick') ?? '')?.[1] ?? ''))).find((v) => v !== '' && v !== asistencias('plaza'));
    expect(plazaNueva, 'hay otra plaza que asignar').toBeTruthy();
    await marcarFila(page, casillas.first());
    const plaza = await esperarSrc(page, 'asistente_plaza_asignar', () => page.locator(`#main [onclick*="fnjs_cmb_plaza"][onclick*="'${plazaNueva}')"]`).first().click());
    expect(plaza.success, plaza.mensaje).toBe(true);
    expect(asistencias('plaza'), 'la plaza ha cambiado').toBe(plazaNueva);
    await abrirPantalla(page, dossier, 'input.sel', true);
  }

  // --- «Cambiar actividad»: solo se comprueba que el formulario lleva su cápsula (no se guarda).
  await marcarFila(page, casillas.first());
  await page.locator('#main [onclick*="fnjs_mover(this.form"]').first().click();
  await expect(page.locator('#main #frm_mover input[name="ctx_guardar"]')).toHaveValue(CAPSULA, { timeout: 20_000 });

  // --- Borrar la asistencia desde el dossier de actividades de la persona (cápsula de esa fila).
  // `modo_curso=3`: todas las actividades (la del test es de 2031, fuera del curso actual).
  await abrirPantalla(page, `frontend/dossiers/controller/dossiers_ver.php?pau=p&id_pau=${persona}&obj_pau=PersonaN&id_dossier=1301&permiso=3&modo_curso=3`, 'input.sel', true);
  const filaActiv = page.locator(`#main input.sel[value^="${idActiv}"]`);
  await expect(filaActiv, 'la actividad sale en el dossier de la persona').toHaveCount(1);
  await marcarFila(page, filaActiv);
  // «Modificar asistencia» desde la persona: el formulario lleva su cápsula (no se guarda).
  await page.locator('#main [onclick*="fnjs_modificar(this.form)"]').first().click();
  await expect(page.locator('#main form input[name="ctx_guardar"]').first()).toHaveValue(CAPSULA, { timeout: 20_000 });
  await abrirPantalla(page, `frontend/dossiers/controller/dossiers_ver.php?pau=p&id_pau=${persona}&obj_pau=PersonaN&id_dossier=1301&permiso=3&modo_curso=3`, 'input.sel', true);
  await marcarFila(page, filaActiv);
  const borrado = await esperarSrc(page, 'asistente_eliminar', () => page.locator('#main [onclick*="fnjs_borrar(this.form)"]').first().click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(asistencias('id_nom'), 'ya no está apuntado').toBe('');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
