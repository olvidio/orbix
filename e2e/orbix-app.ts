import { expect, type Page, type Response } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import { diagnoseBackendHtml } from './backend-diagnostics';
import { indexPathForE2e } from './grupmenu';

/**
 * Ayudas para specs de flujos dentro de la aplicación (pantallas cargadas en `#main` de `index.php`).
 *
 * - `abrirApp()` abre `index.php` y empieza a recoger errores JS y respuestas de `/src` que no son JSON.
 * - `abrirPantalla()` carga un controlador frontend en `#main` como hace el menú (`fnjs_update_div`).
 * - `esperarSrc()` espera la respuesta JSON de un endpoint `/src/...` (las peticiones del navegador pasan por
 *   `frontend/shared/controller/src_ajax.php?…`).
 * - `sqlDev()` ejecuta SQL en la BD de desarrollo (contenedor `orbix-db-1`) para limpiar lo que un test haya podido dejar.
 */

export interface Incidencias {
  erroresJs: string[];
  respuestasNoJson: string[];
}

export async function abrirApp(page: Page): Promise<Incidencias> {
  const inc: Incidencias = { erroresJs: [], respuestasNoJson: [] };
  page.on('pageerror', (e) => inc.erroresJs.push(e.message));
  page.on('response', async (r: Response) => {
    const url = r.url();
    if (!/src_ajax\.php|\/src\//.test(url) || r.request().method() !== 'POST') {
      return;
    }
    const body = await r.text().catch(() => '');
    try {
      JSON.parse(body);
    } catch {
      inc.respuestasNoJson.push(`${r.status()} ${url}: ${body.slice(0, 200)}`);
    }
  });
  await page.goto(indexPathForE2e());
  await expect(page.locator('#main')).toBeAttached();
  pilaNueva.add(page);
  return inc;
}

/** Páginas cuya próxima `abrirPantalla` empieza una pila de navegación nueva (ver abajo). */
const pilaNueva = new WeakSet<Page>();

/**
 * Carga `path` (relativo a la raíz de Orbix, con su query) en `#main` y espera a que pinte `esperar`.
 * `soloPresente`: basta con que exista (p. ej. casillas de una rejilla SlickGrid, que Playwright no
 * siempre da por visibles).
 *
 * La primera pantalla que se abre tras `abrirApp` lleva `nav=reset`, como las entradas del menú
 * (`fnjs_link_submenu`): cada test empieza una pila de navegación nueva, y las pantallas siguientes
 * se apilan encima (así «atrás» vuelve a la anterior del mismo test). Sin él, la pila de la sesión
 * del administrador, que comparten todos los specs, pasaba de un spec a otro: tras dar de alta un
 * usuario abriendo directamente su formulario (`rol-restringido`), «atrás» en `usuarios-alta-baja`
 * volvía a ese formulario de alta en vez de a la lista.
 */
export async function abrirPantalla(page: Page, path: string, esperar: string, soloPresente = false): Promise<void> {
  if (pilaNueva.delete(page)) {
    path += (path.includes('?') ? '&' : '?') + 'nav=reset';
  }
  await page.evaluate((p) => {
    // @ts-expect-error función global de Orbix (scripts/index.js.php)
    fnjs_update_div('#main', p);
  }, path);
  const loc = page.locator('#main').locator(esperar).first();
  if (soloPresente) {
    await expect(loc).toBeAttached({ timeout: 20_000 });
  } else {
    await expect(loc).toBeVisible({ timeout: 20_000 });
  }
  await sinErrorDeBackend(page);
}

/** Falla con un mensaje útil si `#main` contiene un fatal de PHP / PDO. */
export async function sinErrorDeBackend(page: Page): Promise<void> {
  const html = await page.locator('#main').innerHTML();
  const diag = diagnoseBackendHtml(html);
  expect(diag, diag ?? '').toBeNull();
  expect(html).not.toMatch(/Fatal error|Uncaught |Warning: |Notice: /);
}

/** Espera la respuesta del endpoint `/src/<ruta>` y devuelve su JSON. */
export async function esperarSrc(page: Page, ruta: string, accion: () => Promise<unknown>): Promise<{ success?: boolean; mensaje?: string; data?: unknown }> {
  const enc = encodeURIComponent(ruta);
  const [resp] = await Promise.all([
    page.waitForResponse((r) => r.url().includes(ruta) || r.url().includes(enc), { timeout: 30_000 }),
    accion(),
  ]);
  const body = await resp.text();
  try {
    return JSON.parse(body);
  } catch {
    throw new Error(`${ruta} no respondió JSON (${resp.status()}): ${body.slice(0, 300)}`);
  }
}

/** Acepta el siguiente `confirm`/`alert` y devuelve su texto. */
export function aceptarDialogos(page: Page): string[] {
  const textos: string[] = [];
  page.on('dialog', async (d) => {
    textos.push(d.message());
    await d.accept();
  });
  return textos;
}

/** SQL en la BD de desarrollo (solo para limpiar datos del propio test). */
export function sqlDev(db: string, sql: string): string {
  return execFileSync('docker', ['exec', 'orbix-db-1', 'psql', '-U', 'postgres', '-d', db, '-Atc', sql], { encoding: 'utf8' }).trim();
}

/** Marca alfabética única para los datos de un test (los VO de nombres no admiten dígitos). */
export function marcaE2e(): string {
  const letras = 'abcdefghijklmnopqrstuvwxyz';
  let n = Date.now();
  let s = '';
  while (n > 0) {
    s = letras[n % 26] + s;
    n = Math.floor(n / 26);
  }
  return 'Zze' + s;
}

/**
 * Crea por la interfaz una actividad sv · n · ca («sin especificar») de la dl `dl`, del 10 al 12 de
 * febrero de 2031, con el nombre `nombre`. Devuelve su id_activ (leído de la BD `comun`, esquema `esquema`).
 */
export async function crearActividad(page: Page, nombre: string, dl: string, esquema: string): Promise<string> {
  const form = page.locator('#main form#modifica');
  await abrirPantalla(page, 'frontend/actividades/controller/actividad_ver.php?mod=nuevo', 'form#modifica');
  // Cada desplegable del tipo recarga el siguiente por AJAX (`actividad_tipo_get`).
  for (const [campo, valor] of [['#isfsv_val', '1'], ['#iasistentes_val', '1'], ['#iactividad_val', '2'], ['#inom_tipo_val', '000']]) {
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('actividad_tipo_get'), { timeout: 15_000 }),
      form.locator(campo).selectOption(valor),
    ]);
    await page.waitForLoadState('networkidle');
  }
  await form.locator('#nom_activ').fill(nombre);
  await form.locator('#f_ini').fill('10/2/2031');
  await form.locator('#f_fin').fill('12/2/2031');
  await form.locator('#dl_org').selectOption(dl);
  const alta = await esperarSrc(page, 'actividad_mutacion_ajax', () => form.locator(`input[onclick*="fnjs_guardar('nuevo')"]`).click());
  expect(alta.success, alta.mensaje).toBe(true);
  const id = sqlDev('comun', `SELECT id_activ FROM "${esquema}".a_actividades_dl WHERE nom_activ = '${nombre.replace(/'/g, "''")}'`);
  expect(id, 'la actividad está en la BD').toMatch(/^\d+$/);
  return id;
}

/** Borra con SQL una actividad de prueba y las fases de su proceso. */
export function borrarActividadSql(esquema: string, nombre: string): void {
  const ids = `(SELECT id_activ FROM "${esquema}".a_actividades_dl WHERE nom_activ = '${nombre.replace(/'/g, "''")}')`;
  sqlDev('comun', `DELETE FROM "${esquema}".a_actividad_proceso_sv WHERE id_activ IN ${ids}`);
  sqlDev('comun', `DELETE FROM "${esquema}".a_actividad_proceso_sf WHERE id_activ IN ${ids}`);
  sqlDev('comun', `DELETE FROM "${esquema}".a_actividades_dl WHERE nom_activ = '${nombre.replace(/'/g, "''")}'`);
}

/** Ejecuta cada paso de limpieza aunque falle alguno; al final relanza el primer error. */
export function limpiar(...pasos: Array<() => void>): void {
  let primero: unknown = null;
  for (const paso of pasos) {
    try {
      paso();
    } catch (e) {
      primero ??= e;
    }
  }
  if (primero !== null) {
    throw primero;
  }
}

/**
 * Crea por la interfaz un numerario de la dl con el apellido `apellido` (marca del test) y devuelve
 * su id_nom (leído de la BD `sv`, esquema `esquemaV`, p. ej. «H-dlbv»).
 */
export async function crearNumerario(page: Page, apellido: string, esquemaV: string): Promise<string> {
  await abrirPantalla(page, `frontend/personas/controller/personas_editar.php?nuevo=1&obj_pau=PersonaN&id_tabla=n&apellido1=${apellido}`, 'form#frm2');
  const ficha = page.locator('#main form#frm2');
  await ficha.locator('[name="nom"]').fill('Prueba');
  await ficha.locator('[name="apellido1"]').fill(apellido);
  const alta = await esperarSrc(page, 'persona_update', () => ficha.locator('th[onclick*="fnjs_guardar"]').click());
  expect(alta.success, alta.mensaje).toBe(true);
  const id = sqlDev('sv', `SELECT id_nom FROM "${esquemaV}".p_numerarios WHERE apellido1 = '${apellido}'`);
  expect(id, 'el numerario está en la BD').toMatch(/^\d+$/);
  await page.waitForLoadState('networkidle');
  return id;
}
