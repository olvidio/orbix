import { expect, request as pwRequest, test, type APIRequestContext, type APIResponse } from '@playwright/test';

/**
 * Contrato con las apps (orbix-android y las que vengan): lo que hacen por HTTP, sin navegador ni
 * formulario, con la cookie PHPSESSID como OkHttp. Las rutas van directas (`…/orbix/src/…`), como
 * las monta `buildSrcUrl` en la app.
 *
 * - `/src/usuarios/app_session` dice si hay sesión, sin pedirla.
 * - Una ruta `/src/...` sin sesión responde JSON
 *   `{success: false, data: {code: "auth_required"}}` y la cabecera `X-Orbix-Auth-Required: 1`,
 *   no el formulario HTML de login (lo emite `login.php`, que incluye `global_object.inc`).
 * - `/src/usuarios/app_login` (JSON) abre la sesión y, con su cookie, las rutas `/src/...` responden.
 *
 * No usa la sesión guardada del navegador (`storageState`): cada contexto empieza sin cookies.
 */
const USUARIO = process.env.E2E_USER ?? '';
const CLAVE = process.env.E2E_PASSWORD ?? '';
const ESQUEMA = process.env.E2E_ESQUEMA ?? '';

let ctx: APIRequestContext;

test.beforeEach(async ({ baseURL }) => {
  ctx = await pwRequest.newContext({
    baseURL,
    ignoreHTTPSErrors: true,
    storageState: { cookies: [], origins: [] },
    extraHTTPHeaders: { Accept: 'application/json' },
  });
});

test.afterEach(async () => {
  await ctx.dispose();
});

async function json(r: APIResponse): Promise<{ success?: boolean; mensaje?: string; data: Record<string, unknown> }> {
  const texto = await r.text();
  let cuerpo: { success?: boolean; mensaje?: string; data?: unknown };
  try {
    cuerpo = JSON.parse(texto);
  } catch {
    throw new Error(`${r.url()} no respondió JSON (HTTP ${r.status()}): ${texto.slice(0, 200)}`);
  }
  const data = typeof cuerpo.data === 'string' ? JSON.parse(cuerpo.data || '{}') : (cuerpo.data ?? {});
  return { success: cuerpo.success, mensaje: cuerpo.mensaje, data: data as Record<string, unknown> };
}

test('sin sesión: app_session lo dice y las rutas /src responden auth_required en JSON', async () => {
  const sesion = await json(await ctx.get('src/usuarios/app_session'));
  expect(sesion.success).toBe(true);
  expect(sesion.data.authenticated).toBe(false);

  for (const ruta of ['src/menus/grupmenu_coleccion', 'src/misas/ver_plan_ctr_data']) {
    const r = await ctx.get(ruta);
    expect(r.headers()['x-orbix-auth-required'], `${ruta}: cabecera`).toBe('1');
    const cuerpo = await json(r);
    expect(cuerpo.success, ruta).toBe(false);
    expect(cuerpo.data.code, ruta).toBe('auth_required');
  }
});

test('app_login abre la sesión y con su cookie responden app_session y las rutas /src', async () => {
  test.skip(USUARIO === '' || CLAVE === '', 'sin E2E_USER / E2E_PASSWORD');

  const login = await json(
    await ctx.post('src/usuarios/app_login', {
      data: { username: USUARIO, password: CLAVE, esquema: ESQUEMA },
    }),
  );
  expect(login.success, `app_login: ${login.mensaje ?? ''} ${String(login.data.code ?? '')}`).toBe(true);

  const sesion = await json(await ctx.get('src/usuarios/app_session'));
  expect(sesion.data.authenticated).toBe(true);
  expect(sesion.data.username).toBe(USUARIO);

  const r = await ctx.get('src/menus/grupmenu_coleccion');
  expect(r.headers()['x-orbix-auth-required']).toBeUndefined();
  const menus = await json(r);
  expect(menus.success, menus.mensaje).toBe(true);
});

test('app_login con clave errónea no abre sesión', async () => {
  test.skip(USUARIO === '', 'sin E2E_USER');

  const login = await json(
    await ctx.post('src/usuarios/app_login', {
      data: { username: USUARIO, password: 'clave-que-no-es', esquema: ESQUEMA },
    }),
  );
  expect(login.success).toBe(false);
  const sesion = await json(await ctx.get('src/usuarios/app_session'));
  expect(sesion.data.authenticated).toBe(false);
});
