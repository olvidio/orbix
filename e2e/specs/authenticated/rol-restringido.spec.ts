import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, esperarSrc, limpiar, marcaE2e, sqlDev } from '../../orbix-app';

/**
 * Permisos (fase 3.5): un usuario con rol restringido no ve lo prohibido.
 *
 * Como administrador (sesión del global-setup) se da de alta por la interfaz un usuario
 * «Oficial_dl» sin grupos de menú ni oficinas. Después, en un contexto de navegador limpio, se
 * inicia sesión con él y se comprueba que:
 * - el menú no ofrece la administración de usuarios;
 * - si abre igualmente la lista de usuarios, el backend la niega (`usuario_lista`: rol > 3) y no
 *   se ve ningún usuario.
 *
 * Al final se borran con SQL el usuario y sus preferencias.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const USUARIO = marcaE2e().toLowerCase();
const CLAVE = 'Un4-Clave.Larga!Seg';
const ROL_OFICIAL_DL = '6';
let idUsuario = '';

test.afterAll(() => {
  limpiar(
    () => idUsuario !== '' && sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".web_preferencias WHERE id_usuario = ${idUsuario}`),
    () => sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`),
  );
});

test('usuario con rol restringido: sin administración de usuarios', async ({ page, browser, baseURL }) => {
  // 1. Alta del usuario restringido, como administrador.
  await abrirApp(page);
  await abrirPantalla(page, 'frontend/usuarios/controller/usuario_form.php?nuevo=1&quien=usuario', 'form#frm_usuario');
  const form = page.locator('#main form#frm_usuario');
  await form.locator('#usuario').fill(USUARIO);
  await form.locator('[name="nom_usuario"]').fill('Restringido e2e');
  await form.locator('#id_role').selectOption(ROL_OFICIAL_DL);
  await form.locator('#password').fill(CLAVE);
  await form.locator('#cambio_password').uncheck();
  await form.locator('#has_2fa').uncheck();
  const alta = await esperarSrc(page, 'usuario_guardar', () => form.locator('input[onclick*="fnjs_guardar"]').click());
  expect(alta.success, alta.mensaje).toBe(true);
  idUsuario = sqlDev('sv-e', `SELECT id_usuario FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`);
  expect(idUsuario).toMatch(/^\d+$/);

  // 2. Inicio de sesión con él, en un contexto limpio (sin la sesión del administrador).
  const ctx = await browser.newContext({ baseURL, storageState: { cookies: [], origins: [] } });
  const p = await ctx.newPage();
  const erroresJs: string[] = [];
  p.on('pageerror', (e) => erroresJs.push(e.message));
  p.on('dialog', (d) => d.accept());
  await p.goto('index.php');
  await p.locator('input[name="username"]').fill(USUARIO);
  await p.locator('input[name="password"]').fill(CLAVE);
  const esquema = p.locator('select[name="esquema"]');
  if ((await esquema.count()) > 0) {
    await esquema.selectOption(ESQUEMA_V);
  }
  await p.locator('#frm_login input[type="submit"], form.form-signin input[type="submit"]').first().click({ noWaitAfter: true });
  await expect(p.locator('form#frm_login')).toHaveCount(0, { timeout: 30_000 });
  await expect(p.locator('#main')).toBeAttached({ timeout: 30_000 });

  // 3. El menú no ofrece la administración de usuarios.
  const html = await p.content();
  expect(html, 'sin enlace a la lista de usuarios en el menú').not.toContain('usuarios/controller/usuario_lista.php');

  // 4. Si la abre igualmente, el backend la niega y no se ve ningún usuario.
  await p.evaluate(() => {
    // @ts-expect-error función global de Orbix
    fnjs_update_div('#main', 'frontend/usuarios/controller/usuario_lista.php');
  });
  await p.waitForLoadState('networkidle');
  await expect(p.locator('#main input.sel')).toHaveCount(0);
  const main = await p.locator('#main').innerText();
  expect(main, 'no lista a los usuarios').not.toContain('Restringido e2e');
  expect(main, 'no aparece el administrador').not.toMatch(/\badmin\b/i);
  expect(erroresJs, erroresJs.join('\n')).toEqual([]);
  await ctx.close();
});
