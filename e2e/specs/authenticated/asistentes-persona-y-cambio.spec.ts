import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, crearNumerario, esperarSrc, limpiar, marcaE2e, marcarFila, redEnCalma, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujos de asistencia que solo tenían comprobada la cápsula (`asistentes-alta-baja`), ahora con
 * guardado real por la interfaz, sobre una persona del propio test y tres crt n de la dl del curso
 * actual (A, elegida por SQL; B y C, las dos primeras con plazas libres que ofrece «cambiar actividad»):
 *
 * 1. Apuntarla a A desde su dossier de actividades (1301 → «añadir actividad», `asistente_guardar`).
 * 2. «Cambiar actividad» desde la lista de asistentes de A: pasa a B (`asistente_guardar` con `mod=mover`).
 * 3. Petición de plaza de la persona para la actividad C (`peticiones_guardar`).
 * 4. Tabla de peticiones de B: el enlace de su petición la pasa a C.
 *
 * Al final se borran con SQL sus asistencias, peticiones, marcas de dossier y la persona.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const ESQUEMA = ESQUEMA_V.replace(/v$/, '');
const DL = ESQUEMA.split('-')[1] ?? 'dlb';
const APELLIDO = marcaE2e();
const T = `"${ESQUEMA_V}"`;
const TIPO_CRT_N = '111';
let idNom = '';

// Una opción que no está en un desplegable falla a los 20 s, no al agotar el tiempo del proyecto.
test.use({ actionTimeout: 20_000 });

const asistencias = () => sqlDev('sv-e', `SELECT coalesce(string_agg(id_activ::text, ',' ORDER BY id_activ), '') FROM ${T}.d_asistentes_dl WHERE id_nom = ${idNom || 0}`);
const personas = `(SELECT id_nom FROM ${T}.p_numerarios WHERE apellido1 = '${APELLIDO}')`;

test.afterAll(() => {
  limpiar(
    () => {
      const ids = sqlDev('sv', `SELECT coalesce(string_agg(id_nom::text, ','), '0') FROM ${T}.p_numerarios WHERE apellido1 = '${APELLIDO}'`);
      sqlDev('sv-e', `DELETE FROM ${T}.d_asistentes_dl WHERE id_nom IN (${ids})`);
    },
    () => sqlDev('sv', `DELETE FROM ${T}.dap_plazas_peticion_dl WHERE id_nom IN ${personas}`),
    () => sqlDev('sv', `DELETE FROM ${T}.d_dossiers_abiertos WHERE tabla = 'p' AND id_pau IN ${personas}`),
    () => sqlDev('sv', `DELETE FROM ${T}.p_numerarios WHERE apellido1 = '${APELLIDO}'`),
  );
});

test('asistente: apuntar desde la persona, cambiar de actividad, pedir plaza y pasar por la tabla de peticiones', async ({ page }) => {
  // Un crt n de la dl en estado «actual» y todavía por empezar (del curso en marcha), de un tipo del
  // que haya al menos otro igual: «cambiar actividad» solo ofrece las del mismo tipo.
  const actividadA = sqlDev(
    'comun',
    `SELECT id_activ FROM "${ESQUEMA}".a_actividades_dl a WHERE id_tipo_activ::text LIKE '${TIPO_CRT_N}%' AND status = 2 AND f_ini > now()
       AND EXISTS (SELECT 1 FROM "${ESQUEMA}".a_actividades_dl b WHERE b.id_tipo_activ = a.id_tipo_activ AND b.status = 2 AND b.f_ini > now() AND b.id_activ <> a.id_activ)
     ORDER BY f_ini, id_activ LIMIT 1`,
  );
  test.skip(actividadA === '', 'hacen falta dos crt n de la dl del mismo tipo, en estado actual y por empezar');

  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  idNom = await crearNumerario(page, APELLIDO, ESQUEMA_V);

  // --- 1. Apuntarla a la actividad A desde su dossier de actividades.
  await abrirPantalla(page, `frontend/dossiers/controller/dossiers_ver.php?pau=p&id_pau=${idNom}&obj_pau=PersonaN&id_dossier=1301&permiso=3`, '.links-insert-btn');
  await page.locator(`#main .links-insert-btn[onclick*="id_tipo=${TIPO_CRT_N}&"]`).first().click();
  const alta = page.locator('#main form#frm_actividades_de_una_persona');
  await expect(alta).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await alta.locator('#id_activ').selectOption(actividadA);
  await redEnCalma(page);
  await alta.locator('#observ').fill('apuntado desde la persona por e2e');
  const apuntado = await esperarSrc(page, 'asistente_guardar', () => alta.locator('#guardar').click());
  expect(apuntado.success, `${apuntado.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(asistencias(), 'está apuntada a la actividad A').toBe(actividadA);
  await redEnCalma(page);

  // --- 2. «Cambiar actividad» desde la lista de asistentes de A: pasa a B.
  await abrirPantalla(page, `frontend/dossiers/controller/dossiers_ver.php?pau=a&id_pau=${actividadA}&obj_pau=Actividad&id_dossier=3101&permiso=3`, 'input.sel', true);
  const fila = page.locator(`#main input.sel[value^="${idNom}"]`);
  await expect(fila, 'la persona sale en la lista de asistentes de A').toHaveCount(1);
  await marcarFila(page, fila);
  await page.locator('#main [onclick*="fnjs_mover(this.form"]').first().click();
  const mover = page.locator('#main form#frm_mover');
  await expect(mover).toBeVisible({ timeout: 20_000 });
  // Las dos primeras de la propia dl con plazas libres (la lista lleva un separador con valor 0,
  // actividades de otras dl y, al final de cada texto, «libres/concedidas»; a una llena no deja pasar).
  const [actividadB, actividadC] = await mover.locator('select').first().locator('option').evaluateAll(
    (os, [a, dl]) => (os as HTMLOptionElement[]).filter((o) => /^[1-9]\d*$/.test(o.value) && o.value !== a && o.text.includes(`-${dl}`) && /: [1-9]\d*\/\d+\s*$/.test(o.text)).map((o) => o.value),
    [actividadA, DL],
  );
  expect(actividadB && actividadC, 'se ofrecen otras dos actividades del mismo tipo con plazas libres').toBeTruthy();
  await mover.locator('select').first().selectOption(actividadB);
  await mover.locator('#observ').fill('cambiado de actividad por e2e');
  const movido = await esperarSrc(page, 'asistente_guardar', () => mover.locator('#guardar').click());
  expect(movido.success, `${movido.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(asistencias(), 'ha pasado a la actividad B y ya no está en A').toBe(actividadB);
  expect(sqlDev('sv-e', `SELECT observ FROM ${T}.d_asistentes_dl WHERE id_nom = ${idNom}`)).toBe('cambiado de actividad por e2e');
  await redEnCalma(page);

  // --- 3. Petición de plaza de la persona para la actividad C.
  await abrirPantalla(page, `frontend/actividadplazas/controller/peticiones_activ.php?id_nom=${idNom}&na=n&sactividad=crt`, 'form#frm_peticiones');
  const peticiones = page.locator('#main form#frm_peticiones');
  await peticiones.locator('select').first().selectOption(actividadC);
  await redEnCalma(page);
  const peticion = await esperarSrc(page, 'peticiones_guardar', () => peticiones.locator('input[onclick*="fnjs_guardar"]').click());
  expect(peticion.success, `${peticion.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(sqlDev('sv', `SELECT id_activ || '|' || orden || '|' || tipo FROM ${T}.dap_plazas_peticion_dl WHERE id_nom = ${idNom}`), 'la petición está en la BD').toBe(`${actividadC}|1|crt`);
  await redEnCalma(page);

  // --- 4. Tabla de peticiones de B: el enlace de su petición la pasa a C.
  await abrirPantalla(page, `frontend/asistentes/controller/tabla_peticiones.php?id_activ_old=${actividadB}`, 'table');
  const enlace = page.locator(`#main [onclick*="fnjs_cambiar_actividad"][onclick*="id_nom=${idNom}"]`);
  await expect(enlace, 'la tabla ofrece mover a la persona a la actividad que pidió').toHaveCount(1);
  const pasado = await esperarSrc(page, 'asistente_guardar', () => enlace.click());
  expect(pasado.success, `${pasado.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(asistencias(), 'ha pasado a la actividad C').toBe(actividadC);

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
