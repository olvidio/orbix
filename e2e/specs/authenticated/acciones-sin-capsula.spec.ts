import { expect, test } from '@playwright/test';
import { indexPathForE2e } from '../../grupmenu';
import { abrirApp, abrirPantalla, redEnCalma, sqlDev } from '../../orbix-app';

/**
 * Pantallas que son una acción (fase 6 del plan de tests): no hacen nada si se abren sin la cápsula
 * que firma la pantalla anterior, y desde esa pantalla piden confirmación.
 *
 * - `encargossacd/propuestas_aprobar.php` aprobaba las propuestas de encargos (y borraba sus tablas)
 *   solo con abrirse; `encargossacd/comprobaciones.php` borraba encargos de centros inactivos.
 * - `actividadestudios/matricular.php?confirmar=si` matricula a todas las personas.
 *
 * El spec **no ejecuta** ninguna de las tres acciones: las abre sin cápsula y cancela la confirmación.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const ESQUEMA = ESQUEMA_V.replace(/v$/, '');

/** Filas de las tablas que tocarían las tres acciones. */
function recuento(): string {
  const sve = sqlDev(
    'sv-e',
    `SELECT (SELECT count(*) FROM "${ESQUEMA_V}".propuesta_encargos_sacd) || '/' || (SELECT count(*) FROM "${ESQUEMA_V}".propuesta_encargo_sacd_horario)
            || '/' || (SELECT count(*) FROM "${ESQUEMA_V}".encargos_sacd) || '/' || (SELECT count(*) FROM "${ESQUEMA_V}".encargo_sacd_horario)`,
  );
  const comun = sqlDev('comun', `SELECT count(*) FROM "${ESQUEMA}".encargos`);
  const sv = sqlDev('sv', `SELECT count(*) FROM "${ESQUEMA_V}".d_matriculas_activ_dl`);
  return `${sve} · ${comun} · ${sv}`;
}

test('abiertas sin cápsula, las acciones responden «no autorizada» y no cambian nada', async ({ page }) => {
  await page.goto(indexPathForE2e());
  await expect(page.locator('#main')).toBeAttached();
  const base = new URL('.', page.url()).href;
  const antes = recuento();

  const acciones: Array<[string, Record<string, string>]> = [
    ['frontend/encargossacd/controller/propuestas_aprobar.php', { sf: '0' }],
    ['frontend/encargossacd/controller/comprobaciones.php', { que: 'ctr' }],
    ['frontend/actividadestudios/controller/matricular.php', { confirmar: 'si' }],
  ];
  for (const [ruta, form] of acciones) {
    const res = await page.request.post(new URL(ruta, base).href, { form, failOnStatusCode: false });
    const cuerpo = await res.text();
    // En el idioma del usuario de E2E (castellano o catalán).
    expect(cuerpo, ruta).toMatch(/Operaci(ón|ó) no autori(z|tz)ada/);
    expect(cuerpo, ruta).not.toMatch(/Fatal error|Uncaught /);
  }

  expect(recuento(), 'ninguna tabla ha cambiado').toBe(antes);
});

test('desde su pantalla, aprobar propuestas y comprobar encargos piden confirmación', async ({ page }) => {
  const inc = await abrirApp(page);
  const main = page.locator('#main');
  const antes = recuento();
  const preguntas: string[] = [];
  page.on('dialog', async (d) => {
    preguntas.push(d.message());
    await d.dismiss();
  });
  const peticiones: string[] = [];
  page.on('request', (r) => {
    if (/propuestas_aprobar\.php|comprobaciones\.php|comprobaciones_ctr|propuestas_aprobar(\?|$)/.test(r.url())) {
      peticiones.push(r.url());
    }
  });

  // Listas de encargos → «Eliminar encargos de centros inexistentes»
  await abrirPantalla(page, 'frontend/encargossacd/controller/listas_index.php', 'span.link[onclick*="fnjs_comprobaciones"]');
  await main.locator('span.link[onclick*="fnjs_comprobaciones"]').click();
  await expect.poll(() => preguntas.length).toBe(1);

  // Menú de propuestas → «aprobar las propuestas»; el enlace lleva la cápsula firmada por el menú.
  await abrirPantalla(page, 'frontend/encargossacd/controller/propuestas_menu.php', 'span.link[onclick*="fnjs_aprobar_propuestas"]');
  expect(await main.innerHTML(), 'el enlace de aprobar lleva su cápsula').toMatch(/propuestas_aprobar\.php\?[^']*ctx_aprobar=[\w%.-]{20,}/);
  await main.locator('span.link[onclick*="fnjs_aprobar_propuestas"]').click();
  await expect.poll(() => preguntas.length).toBe(2);

  await redEnCalma(page);
  expect(peticiones, 'al cancelar no se pide nada').toEqual([]);
  expect(recuento(), 'ninguna tabla ha cambiado').toBe(antes);
  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
});
