import fs from 'node:fs';
import path from 'node:path';
import { expect, test, type Browser, type Page } from '@playwright/test';
import { abrirApp, abrirPantalla, esperarSrc, limpiar, marcaE2e, redEnCalma, sqlDev } from '../../orbix-app';

/**
 * Usuarios con un rol limitado a una persona, casa o centro (rol «pau» de `aux_roles`): p-sacd, casa y
 * centro sv. Revisión de seguridad del 2026-11-18, punto 2: esos roles solo pueden pedir las pantallas de
 * sus menús y lo que estas usan (`src/shared/config/acceso_rol_pau.php`, `AccesoRolPau`).
 *
 * Como administrador se da de alta por la interfaz un usuario de cada rol y se le ponen (con SQL) el mismo
 * pau y los mismos grupos (permisos de menú) que tiene otro usuario de ese rol. Después, en un contexto
 * limpio, se inicia sesión con él y se abren todas las entradas de todos sus grupos de menú:
 * - ninguna acaba en un error del servidor;
 * - ninguna petición queda fuera de la lista (`log/acceso_pau_denegado.log` no gana líneas de ese pau);
 * - una pantalla de oficina pedida directamente sí queda fuera (se apunta; sin `ORBIX_ACCESO_PAU=exigir`
 *   no se corta, por eso el spec mira el registro y no la respuesta).
 *
 * Con `log/acceso_pau_registrar` presente, `AccesoRolPau` apunta además todo lo que piden
 * (`log/acceso_pau.log`).
 *
 * «Centro sf» no se prueba: es un rol solo de sf y la instancia E2E es de sv.
 * Al final se borran con SQL los usuarios y sus preferencias.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const CLAVE = 'Un4-Clave.Larga!Seg';
const ROLES = [
  { id: '20', nombre: 'p-sacd', pau: 'sacd' },
  { id: '22', nombre: 'casa', pau: 'cdc' },
  { id: '23', nombre: 'Centro sv', pau: 'ctr' },
];
const DENEGADO = path.resolve(__dirname, '../../../log/acceso_pau_denegado.log');
const OFICINA = 'frontend/personas/controller/personas_select.php';

/** Rutas denegadas a ese pau desde `desde` (el registro lo escribe el servidor, en la misma carpeta). */
function denegadas(pau: string, desde: number): string[] {
  if (!fs.existsSync(DENEGADO)) {
    return [];
  }
  return fs
    .readFileSync(DENEGADO, 'utf8')
    .split('\n')
    .map((l) => l.split('\t'))
    .filter((c) => c.length >= 4 && c[1] === pau && Date.parse(c[0]) >= desde)
    .map((c) => `${c[2]} ${c[3]}`);
}
const marca = marcaE2e().toLowerCase().slice(0, 14);
const creados: string[] = [];

test.afterAll(() => {
  for (const usuario of creados) {
    limpiar(
      () => sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".web_preferencias WHERE id_usuario IN (SELECT id_usuario FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${usuario}')`),
      () => sqlDev('sv-e', `DELETE FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${usuario}'`),
    );
  }
});

async function crearUsuario(page: Page, usuario: string, idRole: string): Promise<void> {
  await abrirPantalla(page, 'frontend/usuarios/controller/usuario_form.php?nuevo=1&quien=usuario', 'form#frm_usuario');
  const form = page.locator('#main form#frm_usuario');
  await form.locator('#usuario').fill(usuario);
  await form.locator('[name="nom_usuario"]').fill('Pau e2e');
  await form.locator('#id_role').selectOption(idRole);
  await form.locator('#password').fill(CLAVE);
  await form.locator('#cambio_password').uncheck();
  await form.locator('#has_2fa').uncheck();
  creados.push(usuario);
  const alta = await esperarSrc(page, 'usuario_guardar', () => form.locator('input[onclick*="fnjs_guardar"]').click());
  expect(alta.success, alta.mensaje).toBe(true);
  // El mismo pau (persona, casas o centro) y los mismos grupos (de ellos salen los permisos de menú) que
  // otro usuario de ese rol.
  const ref = `(SELECT id_usuario FROM "${ESQUEMA_V}".aux_usuarios WHERE id_role = ${idRole} AND coalesce(csv_id_pau, '') <> '' AND usuario <> '${usuario}' ORDER BY id_usuario LIMIT 1)`;
  const nuevo = `(SELECT id_usuario FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${usuario}')`;
  sqlDev('sv-e', `UPDATE "${ESQUEMA_V}".aux_usuarios SET csv_id_pau = (SELECT csv_id_pau FROM "${ESQUEMA_V}".aux_usuarios WHERE id_usuario = ${ref}) WHERE usuario = '${usuario}'`);
  sqlDev('sv-e', `INSERT INTO "${ESQUEMA_V}".aux_cross_usuarios_grupos (id_usuario, id_grupo) SELECT ${nuevo}, id_grupo FROM "${ESQUEMA_V}".aux_cross_usuarios_grupos WHERE id_usuario = ${ref}`);
  expect(sqlDev('sv-e', `SELECT coalesce(csv_id_pau, '') FROM "${ESQUEMA_V}".aux_usuarios WHERE usuario = '${usuario}'`), 'tiene pau').not.toBe('');
}

async function entrar(browser: Browser, baseURL: string | undefined, usuario: string): Promise<Page> {
  const ctx = await browser.newContext({ baseURL, storageState: { cookies: [], origins: [] } });
  const p = await ctx.newPage();
  p.on('dialog', (d) => d.dismiss());
  await p.goto('index.php');
  await p.locator('input[name="username"]').fill(usuario);
  await p.locator('input[name="password"]').fill(CLAVE);
  const esquema = p.locator('select[name="esquema"]');
  if ((await esquema.count()) > 0) {
    await esquema.selectOption(ESQUEMA_V);
  }
  await p.locator('#frm_login input[type="submit"], form.form-signin input[type="submit"]').first().click({ noWaitAfter: true });
  await expect(p.locator('form#frm_login')).toHaveCount(0, { timeout: 30_000 });
  await expect(p.locator('#main')).toBeAttached({ timeout: 30_000 });
  return p;
}

for (const rol of ROLES) {
  test(`rol ${rol.nombre}: abre todas las entradas de sus menús`, async ({ page, browser, baseURL }) => {
    test.setTimeout(10 * 60_000);
    const usuario = `${marca}${rol.id}`;
    await abrirApp(page);
    await crearUsuario(page, usuario, rol.id);

    const desde = Math.floor(Date.now() / 1000) * 1000;
    const p = await entrar(browser, baseURL, usuario);
    const grupos = sqlDev('comun', `SELECT string_agg(DISTINCT id_grupmenu::text, ',') FROM public.ref_grupmenu_rol WHERE id_role = ${rol.id}`)
      .split(',')
      .filter((g) => g !== '');
    expect(grupos.length, 'el rol tiene grupos de menú').toBeGreaterThan(0);

    const fallos: string[] = [];
    let abiertas = 0;
    for (const grupo of grupos) {
      await p.goto(`index.php?id_grupmenu=${grupo}`);
      await expect(p.locator('#main')).toBeAttached({ timeout: 30_000 });
      await redEnCalma(p, 30_000);
      const entradas = await p
        .locator('[onclick*="fnjs_link_submenu"]')
        .evaluateAll((els) => [...new Set(els.map((e) => e.getAttribute('onclick') ?? '').filter((o) => !o.includes("fnjs_link_submenu('',")))]);
      for (const onclick of entradas) {
        await p.evaluate((codigo) => {
          // eslint-disable-next-line no-eval -- el onclick del menú, tal cual
          (0, eval)(codigo);
        }, onclick);
        try {
          await redEnCalma(p, 30_000);
        } catch (e) {
          fallos.push(`${onclick}: ${(e as Error).message}`);
          continue;
        }
        abiertas++;
        const html = await p.locator('#main').innerHTML();
        if (/Fatal error|Uncaught |SQLSTATE/.test(html)) {
          fallos.push(`${onclick}: error del servidor`);
        }
      }
    }
    console.log(`${rol.nombre}: grupos ${grupos.length}, entradas abiertas ${abiertas}`);
    expect(abiertas, 'ha abierto alguna entrada').toBeGreaterThan(0);
    expect(fallos, fallos.join('\n')).toEqual([]);
    expect(denegadas(rol.pau, desde), 'lo que piden sus menús está en la lista').toEqual([]);

    // Una pantalla de oficina pedida directamente queda fuera.
    const oficina = await p.request.post(OFICINA, { form: { e2e: '1' }, failOnStatusCode: false });
    expect(denegadas(rol.pau, desde), 'se apunta la pantalla de oficina').toContain(`POST ${OFICINA}`);
    if (process.env.E2E_ACCESO_PAU_EXIGIDO === '1') {
      // Con `ORBIX_ACCESO_PAU=exigir` en el servidor, además se corta.
      expect(oficina.status(), 'la pantalla de oficina se niega').toBe(403);
    }
    await p.context().close();
  });
}
