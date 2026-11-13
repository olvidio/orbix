import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, borrarActividadSql, crearActividad, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de procesos (fase 5.3), por la interfaz:
 * crear una actividad → «fases de la actividad» (`actividad_proceso.php`) → la tabla de fases del
 * proceso (creado con la actividad) → marcar la primera fase sin completar, con una observación →
 * guardar (`actividad_proceso_update`) → está en la BD y la tabla se repinta con la fase marcada →
 * desmarcarla → guardar → vuelve a estar sin completar.
 *
 * La actividad (y las fases de su proceso) se borra con SQL al final.
 */
const ESQUEMA = (process.env.E2E_ESQUEMA ?? 'H-dlbv').replace(/v$/, '');
const DL = ESQUEMA.split('-')[1] ?? 'dlb';
const NOMBRE = `${marcaE2e()} proceso`;
const TIPO: [string, string, string, string] = ['1', '7', '1', '000'];
const OBSERV = 'e2e: revisado & conforme';

test.afterAll(() => {
  limpiar(() => borrarActividadSql(ESQUEMA, NOMBRE));
});

test('fases de una actividad: marcar y desmarcar una fase', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');

  // Tipo 171000 (sv · sss+ · crt): en la BD de desarrollo su proceso sí es coherente (ver el salto de abajo).
  const idActiv = await crearActividad(page, NOMBRE, DL, ESQUEMA, TIPO);
  const fase = (idItem: string, campos: string) => sqlDev('comun', `SELECT ${campos} FROM "${ESQUEMA}".a_actividad_proceso_sv WHERE id_item = ${idItem}`);

  // El diseño del proceso de la actividad tiene que ser coherente: en la BD de desarrollo muchos
  // tipos de proceso tienen fases que dependen de otra que no está en el proceso (duda 15 del plan
  // de tests) y entonces la aplicación responde «Proceso mal diseñado» al marcar cualquier fase.
  const previasQueFaltan = sqlDev('comun', `WITH t AS (SELECT id_fase, json_fases_previas FROM "${ESQUEMA}".a_tareas_proceso
      WHERE id_tipo_proceso = (SELECT min(id_tipo_proceso) FROM "${ESQUEMA}".a_actividad_proceso_sv WHERE id_activ = ${idActiv}))
    SELECT count(*) FROM t, json_array_elements(t.json_fases_previas::json) j
    WHERE j->>'id_fase' <> '' AND NOT EXISTS (SELECT 1 FROM t t2 WHERE t2.id_fase = (j->>'id_fase')::int)`);
  test.skip(previasQueFaltan !== '0', `el proceso de la actividad tiene ${previasQueFaltan} dependencias de fases que no están en él (datos de desarrollo)`);

  await page.waitForLoadState('networkidle');
  await abrirPantalla(page, `frontend/procesos/controller/actividad_proceso.php?id_activ=${idActiv}`, '#div_proceso table');
  await sinErrorDeBackend(page);
  const pendiente = main.locator('#div_proceso input[name="completado"]:not(:checked)').first();
  await expect(pendiente, 'hay alguna fase sin completar que el usuario puede marcar').toBeVisible();
  const idItem = ((await pendiente.getAttribute('id')) ?? '').replace('comp', '');
  expect(idItem).toMatch(/^\d+$/);
  expect(fase(idItem, 'completado')).toBe('f');

  const casilla = main.locator(`#comp${idItem}`);
  const guardar = main.locator(`#div_proceso input[onclick^="fnjs_guardar(${idItem},"]`);
  /** Guarda y espera a que la tabla se repinte después de la respuesta. */
  const guardarYRepintar = async () => {
    const repintado = page.waitForResponse((r) => r.url().includes('actividad_proceso_get'), { timeout: 20_000 });
    const r = await esperarSrc(page, 'actividad_proceso_update', () => guardar.click());
    await repintado;
    await page.waitForLoadState('networkidle');
    return r;
  };

  await casilla.check();
  await main.locator(`#observ${idItem}`).fill(OBSERV);
  const marcada = await guardarYRepintar();
  expect(marcada.success, `${marcada.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(fase(idItem, `completado || '|' || observ`)).toBe(`true|${OBSERV}`);
  await expect(casilla, 'la tabla repintada muestra la fase marcada').toBeChecked();
  await expect(main.locator(`#observ${idItem}`)).toHaveValue(OBSERV);

  await casilla.uncheck();
  const desmarcada = await guardarYRepintar();
  expect(desmarcada.success, `${desmarcada.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(fase(idItem, 'completado')).toBe('f');
  await expect(casilla).not.toBeChecked();

  expect(dialogos.length, `solo el aviso del alta de la actividad: ${dialogos.join(' / ')}`).toBe(1);
  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
