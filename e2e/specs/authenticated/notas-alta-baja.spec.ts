import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, crearNumerario, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de notas (fase 3.4), por la interfaz:
 * crear un numerario → abrir su dossier de notas (1011) → «añadir nota» → asignatura, situación
 * «superada», acta de la propia dl (que no existe ni está firmada) y fecha → guardar
 * (`persona_nota_nueva`) → está en la BD → marcarla en la lista → borrar con confirmación
 * (`persona_nota_eliminar`) → ya no está.
 *
 * La persona es del propio test; al final se borran con SQL sus notas y la persona.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const DL = (ESQUEMA_V.split('-')[1] ?? 'dlbv').replace(/v$/, '');
const APELLIDO = marcaE2e();
const ASIGNATURA_INTRO_FILOSOFIA = '1101';
const SITUACION_SUPERADA = '1';
let idNom = '';

const notas = (campos: string) => sqlDev('sv', `SELECT ${campos} FROM "${ESQUEMA_V}".e_notas_dl WHERE id_nom = ${idNom || 0} ORDER BY id_nivel`);

test.afterAll(() => {
  limpiar(
    () => {
      if (idNom !== '') {
        sqlDev('sv', `DELETE FROM "${ESQUEMA_V}".e_notas_dl WHERE id_nom = ${idNom}`);
      }
    },
    () => {
      if (idNom !== '') {
        sqlDev('sv', `DELETE FROM "${ESQUEMA_V}".d_dossiers_abiertos WHERE tabla = 'p' AND id_pau = ${idNom}`);
      }
    },
    () => sqlDev('sv', `DELETE FROM "${ESQUEMA_V}".p_numerarios WHERE apellido1 = '${APELLIDO}'`),
  );
});

test('nota: añadir una asignatura superada a una persona y borrarla', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  idNom = await crearNumerario(page, APELLIDO, ESQUEMA_V);

  const dossier = `frontend/dossiers/controller/dossiers_ver.php?pau=p&id_pau=${idNom}&obj_pau=PersonaN&id_dossier=1011&permiso=3`;
  await abrirPantalla(page, dossier, '.links-insert-btn');
  await page.locator('#main .links-insert-btn').first().click();
  const form = page.locator('#main form#form_notas_de_una_persona');
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);

  await form.locator('#tipo_acta_a').check();
  await form.locator('#acta_sigla').selectOption(DL);
  await form.locator('#acta_num').fill('999/31');
  await form.locator('#id_nivel').selectOption(ASIGNATURA_INTRO_FILOSOFIA);
  await form.locator('#id_situacion').selectOption(SITUACION_SUPERADA);
  await form.locator('#f_acta').fill('15/6/2031');
  const alta = await esperarSrc(page, 'persona_nota_nueva', () => form.locator('input[onclick*="fnjs_guardar(this.form)"]').click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(notas(`id_nivel || '|' || id_situacion || '|' || acta || '|' || f_acta`)).toBe(`${ASIGNATURA_INTRO_FILOSOFIA}|${SITUACION_SUPERADA}|${DL} 999/31|2031-06-15`);

  await page.waitForLoadState('networkidle');
  await abrirPantalla(page, dossier, 'input.sel', true);
  const casillas = page.locator('#main input.sel');
  await expect(casillas, 'la nota sale en la lista').toHaveCount(1);
  await casillas.first().click({ force: true });
  const borrado = await esperarSrc(page, 'persona_nota_eliminar', () => page.locator('#main [onclick*="fnjs_borrar"]').first().click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(notas('id_nivel'), 'ya no está en la BD').toBe('');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
