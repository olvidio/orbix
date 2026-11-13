import { expect, test, type Locator } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de cambios (fase 5.3), por la interfaz:
 * crear un usuario → sus «avisos de cambios» (`usuario_form_avisos.php`) → «añadir aviso» → objeto,
 * fase (o estado) de referencia, por dónde avisar y una propiedad → guardar (primero el objeto y luego
 * las propiedades) → está en la BD y en la lista → marcarlo → eliminar con confirmación
 * (`cambio_usuario_objeto_pref_eliminar`) → ya no está.
 *
 * Al final se borran con SQL los avisos del usuario del test y el usuario.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const USUARIO = marcaE2e().toLowerCase();
const T = `"${ESQUEMA_V}"`;
const idUsuarioSql = () => sqlDev('sv-e', `SELECT id_usuario FROM ${T}.aux_usuarios WHERE usuario = '${USUARIO}'`);
/** Valores no vacíos de las opciones de un desplegable. */
const opciones = (select: Locator) => select.locator('option').evaluateAll((os) => os.map((o) => (o as HTMLOptionElement).value).filter((v) => v !== ''));

test.afterAll(() => {
  const id = idUsuarioSql();
  limpiar(
    () => {
      if (/^\d+$/.test(id)) {
        const objetos = `(SELECT id_item_usuario_objeto FROM ${T}.av_cambios_usuario_objeto_pref WHERE id_usuario = ${id})`;
        sqlDev('sv-e', `DELETE FROM ${T}.av_cambios_usuario_propiedades_pref WHERE id_item_usuario_objeto IN ${objetos}`);
        sqlDev('sv-e', `DELETE FROM ${T}.av_cambios_usuario_objeto_pref WHERE id_usuario = ${id}`);
      }
    },
    () => sqlDev('sv-e', `DELETE FROM ${T}.aux_usuarios WHERE usuario = '${USUARIO}'`),
  );
});

test('avisos de cambios de un usuario: añadir y eliminar', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');

  // --- Un usuario propio (como en usuarios-alta-baja)
  const fichaUsuario = main.locator('form#frm_usuario');
  await abrirPantalla(page, 'frontend/usuarios/controller/usuario_form.php?quien=usuario&nuevo=1', 'form#frm_usuario');
  await fichaUsuario.locator('#usuario').fill(USUARIO);
  await fichaUsuario.locator('[name="nom_usuario"]').fill('Usuario e2e avisos');
  await fichaUsuario.locator('#id_role').selectOption('6');
  await fichaUsuario.locator('#password').fill('Un4-Clave.Larga!Seg');
  await fichaUsuario.locator('[name="email"]').fill('e2e@example.invalid');
  const alta = await esperarSrc(page, 'usuario_guardar', () => fichaUsuario.locator('input[onclick*="fnjs_guardar"]').click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  const idUsuario = idUsuarioSql();
  expect(idUsuario, 'el usuario está en la BD').toMatch(/^\d+$/);
  const avisos = () => sqlDev('sv-e', `SELECT count(*) FROM ${T}.av_cambios_usuario_objeto_pref WHERE id_usuario = ${idUsuario}`);
  await page.waitForLoadState('networkidle');

  // --- Añadir un aviso
  await abrirPantalla(page, `frontend/cambios/controller/usuario_form_avisos.php?id_usuario=${idUsuario}&quien=usuario`, 'form#avisos');
  expect(avisos()).toBe('0');
  await main.locator('form#avisos input[onclick*="fnjs_add_cambio"]').click();
  const form = main.locator('form#usuario_pref');
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  const objeto = (await opciones(form.locator('#objeto')))[0];
  expect(objeto, 'hay algún objeto en el que mirar cambios').toBeTruthy();
  await form.locator('#objeto').selectOption(objeto);
  const propiedad = main.locator(`#propiedades_obj input[type="checkbox"][name="${objeto}[]"]`).first();
  await expect(propiedad).toBeVisible({ timeout: 20_000 });
  await page.waitForLoadState('networkidle');
  const faseRef = main.locator('#id_fase_ref');
  await faseRef.selectOption((await opciones(faseRef))[0]);
  const avisoTipo = form.locator('#aviso_tipo');
  await avisoTipo.selectOption((await opciones(avisoTipo))[0]);
  await form.locator('[name="aviso_on"]').check();
  await propiedad.check();
  const guardado = await esperarSrc(page, 'cambio_usuario_propiedad_pref_guardar_todas', () => main.locator('input[onclick*="fnjs_grabar_todo"]').click());
  expect(guardado.success, `${guardado.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(avisos(), 'el aviso está en la BD').toBe('1');
  expect(sqlDev('sv-e', `SELECT objeto || '|' || aviso_on FROM ${T}.av_cambios_usuario_objeto_pref WHERE id_usuario = ${idUsuario}`)).toBe(`${objeto}|true`);
  expect(
    sqlDev('sv-e', `SELECT count(*) FROM ${T}.av_cambios_usuario_propiedades_pref WHERE id_item_usuario_objeto IN (SELECT id_item_usuario_objeto FROM ${T}.av_cambios_usuario_objeto_pref WHERE id_usuario = ${idUsuario})`),
    'con su propiedad',
  ).toBe('1');

  // --- Tras guardar se vuelve a la lista de avisos: marcarlo y eliminarlo
  const casilla = main.locator('form#avisos input.sel');
  await expect(casilla).toHaveCount(1, { timeout: 20_000 });
  await sinErrorDeBackend(page);
  await casilla.click({ force: true });
  const antes = dialogos.length;
  const borrado = await esperarSrc(page, 'cambio_usuario_objeto_pref_eliminar', () => main.locator('form#avisos [onclick*="fnjs_del_cambio"]').first().click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(antes);
  expect(avisos(), 'ya no está en la BD').toBe('0');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
