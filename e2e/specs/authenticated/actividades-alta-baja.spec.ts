import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de actividades (fase 3.3), por la interfaz:
 * ficha nueva (tipo con los desplegables encadenados) → «crear ficha» (`actividad_nuevo`) →
 * está en la BD → abrir su ficha → modificar → guardar (`actividad_editar`) → buscarla en el
 * listado → marcarla → borrar con confirmación (`actividad_eliminar`) → ya no está.
 *
 * Fechas en un año lejano para no cruzarse con datos reales. Los datos llevan una marca propia y
 * se borran con SQL al final (también las fases del proceso) aunque el test falle a medias.
 */
const ESQUEMA = (process.env.E2E_ESQUEMA ?? 'H-dlbv').replace(/v$/, '');
const DL = ESQUEMA.split('-')[1] ?? 'dlb';
const marca = marcaE2e();
const NOMBRE = `${marca} actividad`;

const actividad = (campos: string) => sqlDev('comun', `SELECT ${campos} FROM "${ESQUEMA}".a_actividades_dl WHERE nom_activ = '${NOMBRE}'`);

test.afterAll(() => {
  const ids = `(SELECT id_activ FROM "${ESQUEMA}".a_actividades_dl WHERE nom_activ = '${NOMBRE}')`;
  sqlDev('comun', `DELETE FROM "${ESQUEMA}".a_actividad_proceso_sv WHERE id_activ IN ${ids}`);
  sqlDev('comun', `DELETE FROM "${ESQUEMA}".a_actividad_proceso_sf WHERE id_activ IN ${ids}`);
  sqlDev('comun', `DELETE FROM "${ESQUEMA}".a_actividades_dl WHERE nom_activ = '${NOMBRE}'`);
});

test('actividad: alta, modificación y borrado desde el listado', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const form = page.locator('#main form#modifica');

  await abrirPantalla(page, 'frontend/actividades/controller/actividad_ver.php?mod=nuevo', 'form#modifica');
  // Tipo: sv · n · ca · sin especificar. Cada desplegable recarga el siguiente por AJAX
  // (`actividad_tipo_get`): hay que esperar la respuesta antes de elegir en el siguiente.
  const elegir = async (campo: string, valor: string) => {
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('actividad_tipo_get'), { timeout: 15_000 }),
      form.locator(campo).selectOption(valor),
    ]);
    await page.waitForLoadState('networkidle');
  };
  await elegir('#isfsv_val', '1');
  await elegir('#iasistentes_val', '1');
  await elegir('#iactividad_val', '2');
  await elegir('#inom_tipo_val', '000');
  await form.locator('#nom_activ').fill(NOMBRE);
  await form.locator('#f_ini').fill('10/2/2031');
  await form.locator('#f_fin').fill('12/2/2031');
  await form.locator('#dl_org').selectOption(DL);
  const alta = await esperarSrc(page, 'actividad_mutacion_ajax', () => form.locator(`input[onclick*="fnjs_guardar('nuevo')"]`).click());
  expect(alta.success, `${alta.mensaje} ${dialogos.join(' / ')}`).toBe(true);

  const idActiv = actividad('id_activ');
  expect(idActiv, 'la actividad está en la BD').toMatch(/^\d+$/);
  expect(actividad(`id_tipo_activ || '|' || f_ini || '|' || f_fin || '|' || dl_org`)).toBe(`112000|2031-02-10|2031-02-12|${DL}`);

  await abrirPantalla(page, `frontend/actividades/controller/actividad_ver.php?sel[]=${idActiv}`, 'form#modifica');
  await expect(form.locator('#nom_activ')).toHaveValue(NOMBRE);
  await form.locator('#observ').fill('modificada por e2e');
  await form.locator('#plazas').fill('25');
  // Tras guardar, la ficha avisa y vuelve atrás (nav_atras.php + recarga de #main): esperar esa
  // navegación para que no pise el listado que se abre después.
  const vueltaAtras = page.waitForResponse((r) => r.url().includes('nav_atras'), { timeout: 30_000 });
  const cambio = await esperarSrc(page, 'actividad_mutacion_ajax', () => form.locator(`input[onclick*="fnjs_guardar('editar')"]`).click());
  expect(cambio.success, cambio.mensaje).toBe(true);
  expect(actividad(`observ || '|' || plazas`)).toBe('modificada por e2e|25');
  await vueltaAtras;
  // nav_atras devuelve a la ficha de alta: esperar a que esté pintada de nuevo.
  await expect(page.locator('#main #isfsv_val')).toBeVisible({ timeout: 20_000 });

  await abrirPantalla(
    page,
    `frontend/actividades/controller/actividad_select.php?nom_activ=${marca}&periodo=otro&empiezamin=1/1/2031&empiezamax=31/12/2031&year=2031&dl_org=${DL}&status=1&modo=buscar`,
    'input.sel',
    true,
  );
  const casillas = page.locator('#main input.sel');
  await expect(casillas, 'solo sale la actividad del test').toHaveCount(1);
  // La rejilla SlickGrid gestiona la casilla (re-pinta la fila): basta el clic.
  await casillas.first().click({ force: true });
  const borrado = await esperarSrc(page, 'actividad_eliminar', () => page.locator('#main [onclick*="fnjs_borrar"]').first().click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(actividad('id_activ'), 'ya no está en la BD').toBe('');

  await sinErrorDeBackend(page);
  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
