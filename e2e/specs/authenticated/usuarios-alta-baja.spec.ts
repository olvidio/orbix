import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de usuarios (fase 3.4), por la interfaz:
 * lista de usuarios → «nuevo usuario» → datos, rol sin persona ni centro (Oficial_dl), contraseña
 * que cumple la política (se comprueba con `usuario_check_pwd`) → guardar (`usuario_guardar`) → está
 * en la BD con la contraseña cifrada → abrir su ficha → cambiar el nombre → guardar → marcarlo en
 * la lista → eliminar con confirmación (`usuario_eliminar`) → ya no está.
 *
 * Al final se borra con SQL cualquier usuario con la marca del test.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const USUARIO = marcaE2e().toLowerCase();
const ROL_OFICIAL_DL = '6';

const usuario = (campos: string) => sqlDev('sv-e', `SELECT ${campos} FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`);

test.afterAll(() => {
  limpiar(() => sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`));
});

test('usuario: alta, modificación y borrado', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const form = page.locator('#main form#frm_usuario');

  await abrirPantalla(page, 'frontend/usuarios/controller/usuario_lista.php', 'span.link_inv[onclick*="nuevo"]');
  await page.locator('#main span.link_inv[onclick*="nuevo"]').first().click();
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await form.locator('#usuario').fill(USUARIO);
  await form.locator('[name="nom_usuario"]').fill('Usuario de prueba e2e');
  await form.locator('#id_role').selectOption(ROL_OFICIAL_DL);
  await form.locator('#password').fill('Un4-Clave.Larga!Seg');
  await form.locator('[name="email"]').fill('e2e@example.invalid');
  const alta = await esperarSrc(page, 'usuario_guardar', () => form.locator('input[onclick*="fnjs_guardar"]').click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);

  const idUsuario = usuario('id_usuario');
  expect(idUsuario, 'el usuario está en la BD').toMatch(/^\d+$/);
  expect(usuario(`nom_usuario || '|' || id_role || '|' || email`)).toBe(`Usuario de prueba e2e|${ROL_OFICIAL_DL}|e2e@example.invalid`);
  const hash = usuario('password');
  expect(hash, 'la contraseña no se guarda en claro').not.toContain('Un4-Clave');
  expect(hash.length).toBeGreaterThan(20);

  // Tras guardar, la ficha vuelve atrás a la lista (nav_atras): esperar a que esté de nuevo.
  await expect(page.locator('#main span.link_inv[onclick*="nuevo"]')).toBeVisible({ timeout: 20_000 });
  await abrirPantalla(page, `frontend/usuarios/controller/usuario_form.php?id_usuario=${idUsuario}&quien=usuario`, 'form#frm_usuario');
  await expect(form.locator('#usuario')).toHaveValue(USUARIO);
  await form.locator('[name="nom_usuario"]').fill('Usuario e2e modificado');
  const cambio = await esperarSrc(page, 'usuario_guardar', () => form.locator('input[onclick*="fnjs_guardar"]').click());
  expect(cambio.success, cambio.mensaje).toBe(true);
  expect(usuario('nom_usuario')).toBe('Usuario e2e modificado');
  expect(usuario('password'), 'sin contraseña nueva no se cambia').toBe(hash);

  await expect(page.locator('#main span.link_inv[onclick*="nuevo"]')).toBeVisible({ timeout: 20_000 });
  // Lista filtrada por el usuario del test (la rejilla solo pinta las filas visibles). La casilla
  // lleva una cápsula, no el id: basta con que haya una sola fila.
  await abrirPantalla(page, `frontend/usuarios/controller/usuario_lista.php?username=${USUARIO}`, 'input.sel', true);
  const casilla = page.locator('#main input.sel');
  await expect(casilla).toHaveCount(1);
  await casilla.click({ force: true });
  const borrado = await esperarSrc(page, 'usuario_eliminar', () => page.locator('#main [onclick*="fnjs_eliminar()"]').first().click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(usuario('id_usuario'), 'ya no está en la BD').toBe('');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
