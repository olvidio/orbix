import { expect, test } from '@playwright/test';
import { limpiar, marcaE2e, sqlDev } from '../../orbix-app';

/**
 * Recuperar la contraseña sin sesión (duda 3 de la sección 6b del plan), en un navegador limpio:
 * ayuda de acceso → «recuperar contraseña» (pide confirmación; abrir el enlace no envía nada) →
 * enviar → el correo sale enmascarado y la contraseña no ha cambiado → abrir el enlace del correo
 * (se lee de la cola `cola_mails`) → elegir contraseña nueva → iniciar sesión con ella → el enlace
 * ya no vale.
 *
 * El usuario se crea con SQL (con id max + 1: la secuencia de la BD dev va por detrás); al final se borran el usuario, sus preferencias y el correo de la cola.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const USUARIO = marcaE2e().toLowerCase();
const EMAIL = `${USUARIO}@example.invalid`;
const CLAVE_NUEVA = 'Otra-Clave.Larga!Seg9';
const ROL_OFICIAL_DL = 6;
const usuario = (campos: string) => sqlDev('sv-e', `SELECT ${campos} FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`);

test.afterAll(() => {
  limpiar(
    () => sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".web_preferencias WHERE id_usuario IN (SELECT id_usuario FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}')`),
    () => sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${USUARIO}'`),
    () => sqlDev('comun', `DELETE FROM public.cola_mails WHERE mail_to = '${EMAIL}'`),
  );
});

test('recuperar la contraseña con el enlace del correo', async ({ browser, baseURL }) => {
  sqlDev('sv-e', `INSERT INTO "${ESQUEMA_V}".aux_usuarios (id_usuario, usuario, id_role, password, email, nom_usuario) SELECT max(id_usuario) + 1, '${USUARIO}', ${ROL_OFICIAL_DL}, '00', '${EMAIL}', 'Recuperar e2e' FROM "${ESQUEMA_V}".aux_usuarios`);
  const hashInicial = usuario('password');

  const ctx = await browser.newContext({ baseURL, storageState: { cookies: [], origins: [] } });
  const p = await ctx.newPage();
  const recuperar = `frontend/usuarios/controller/recuperar_password.php?username=${USUARIO}&esquema=${ESQUEMA_V}`;

  // 1. Abrir el enlace de la ayuda solo pide confirmación.
  await p.goto(recuperar);
  await expect(p.locator('#btn_enviar_recuperacion')).toBeVisible();
  expect(sqlDev('comun', `SELECT count(*) FROM public.cola_mails WHERE mail_to = '${EMAIL}'`), 'abrir el enlace no envía nada').toBe('0');

  // 2. Enviar: correo enmascarado, contraseña sin cambiar.
  await p.locator('#btn_enviar_recuperacion').click();
  await expect(p.locator('.message.success')).toBeVisible();
  const pagina = await p.content();
  expect(pagina).not.toContain(EMAIL);
  expect(pagina).toContain('@ex*****.invalid');
  expect(usuario('password'), 'la contraseña actual sigue valiendo').toBe(hashInicial);

  // 3. El enlace del correo.
  const mensaje = sqlDev('comun', `SELECT message FROM public.cola_mails WHERE mail_to = '${EMAIL}'`);
  const enlace = /(\S*restablecer_password\.php\?tk=\S+)/.exec(mensaje)?.[1] ?? '';
  expect(enlace, mensaje).not.toBe('');
  const ruta = enlace.slice(enlace.indexOf('frontend/'));

  await p.goto(ruta);
  await expect(p.locator('#password')).toBeVisible();
  await p.locator('#password').fill(CLAVE_NUEVA);
  await p.locator('#password2').fill('no coincide');
  await p.locator('#btn_guardar_password').click();
  await expect(p.locator('.message.error')).toBeVisible();
  await p.locator('#password').fill(CLAVE_NUEVA);
  await p.locator('#password2').fill(CLAVE_NUEVA);
  await p.locator('#btn_guardar_password').click();
  await expect(p.locator('.message.success')).toBeVisible();
  expect(usuario('password')).not.toBe(hashInicial);
  expect(usuario('cambio_password')).toBe('f');

  // 4. El enlace ya no vale.
  await p.goto(ruta);
  await expect(p.locator('#password')).toHaveCount(0);
  await expect(p.locator('.message.error')).toBeVisible();

  // 5. Inicio de sesión con la contraseña nueva.
  await p.goto('index.php');
  await p.locator('input[name="username"]').fill(USUARIO);
  await p.locator('input[name="password"]').fill(CLAVE_NUEVA);
  const esquema = p.locator('select[name="esquema"]');
  if ((await esquema.count()) > 0) {
    await esquema.selectOption(ESQUEMA_V);
  }
  await p.locator('#frm_login input[type="submit"], form.form-signin input[type="submit"]').first().click({ noWaitAfter: true });
  await expect(p.locator('form#frm_login')).toHaveCount(0, { timeout: 30_000 });
  await expect(p.locator('#main')).toBeAttached({ timeout: 30_000 });
  await ctx.close();
});
