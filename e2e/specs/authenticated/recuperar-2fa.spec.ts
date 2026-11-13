import { expect, test } from '@playwright/test';
import { limpiar, marcaE2e, sqlDev } from '../../orbix-app';

/**
 * Recuperar el doble factor sin sesión (duda 4 de la sección 6b del plan), en un navegador limpio:
 * «recuperar 2FA» (pide confirmación; abrir el enlace no envía nada) → enviar → correo
 * enmascarado → abrir el enlace del correo (de la cola `cola_mails`) → pide confirmación →
 * desactivar → el 2FA queda desactivado y el enlace anulado, **sin abrir sesión** (antes el enlace
 * entraba como el usuario sin contraseña) → el enlace ya no vale.
 *
 * El usuario (con 2FA activado pero sin secreto guardado) se crea con SQL; al final se borran el
 * usuario y el correo de la cola.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const USUARIO = marcaE2e().toLowerCase();
const EMAIL = `${USUARIO}@example.invalid`;
const usuario = (campos: string) => sqlDev('sv-e', `SELECT ${campos} FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`);
const correos = (campos: string) => sqlDev('comun', `SELECT ${campos} FROM public.cola_mails WHERE mail_to = '${EMAIL}'`);

test.afterAll(() => {
  limpiar(
    () => sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`),
    () => sqlDev('comun', `DELETE FROM public.cola_mails WHERE mail_to = '${EMAIL}'`),
  );
});

test('recuperar el 2FA: el enlace lo desactiva pero no abre sesión', async ({ browser, baseURL }) => {
  sqlDev('sv-e', `INSERT INTO "${ESQUEMA_V}".aux_usuarios (id_usuario, usuario, id_role, password, email, has_2fa) SELECT max(id_usuario) + 1, '${USUARIO}', 6, '00', '${EMAIL}', true FROM "${ESQUEMA_V}".aux_usuarios`);

  const ctx = await browser.newContext({ baseURL, storageState: { cookies: [], origins: [] } });
  const p = await ctx.newPage();

  await p.goto(`frontend/usuarios/controller/recuperar_2fa.php?username=${USUARIO}&esquema=${ESQUEMA_V}`);
  await expect(p.locator('#btn_enviar_recuperacion_2fa')).toBeVisible();
  expect(correos('count(*)'), 'abrir el enlace no envía nada').toBe('0');
  await p.locator('#btn_enviar_recuperacion_2fa').click();
  await expect(p.locator('.message.success')).toBeVisible();
  const pagina = await p.content();
  expect(pagina).not.toContain(EMAIL);
  expect(pagina).toContain('@ex*****.invalid');

  const enlace = /(\S*recovery\.php\?\S+)/.exec(correos('message'))?.[1] ?? '';
  expect(enlace).not.toBe('');
  const ruta = enlace.slice(enlace.indexOf('frontend/'));

  await p.goto(ruta);
  await expect(p.locator('#btn_desactivar_2fa')).toBeVisible();
  expect(usuario('has_2fa'), 'abrir el enlace no desactiva nada').toBe('t');
  await p.locator('#btn_desactivar_2fa').click();
  await expect(p.locator('.message.success')).toBeVisible();
  expect(usuario(`has_2fa::int || '|' || coalesce(token_recuperacion_2fa, '-')`)).toBe('0|-');

  // Sin sesión: la aplicación sigue pidiendo el login.
  await p.goto('index.php');
  await expect(p.locator('input[name="password"]')).toBeVisible();

  // El enlace ya no vale.
  await p.goto(ruta);
  await expect(p.locator('#btn_desactivar_2fa')).toHaveCount(0);
  await expect(p.locator('.message.error')).toBeVisible();
  await ctx.close();
});
