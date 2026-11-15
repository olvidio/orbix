import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, marcarFila, redEnCalma, sinErrorDeBackend } from '../../orbix-app';

/**
 * Lista de actividades sg: con una actividad marcada, cada botón abre su pantalla (fase 6.4).
 *
 * Antes los botones «cargos», «asistentes» y «lista» cerraban la sesión (a la lista le faltaban los
 * campos ocultos `queSel` e `id_dossier` que firma su formulario) y «ctrs org» no tenía destino; ahora
 * abre el dossier de centros encargados de la actividad (3010). Solo lectura.
 */
const BOTONES: Array<[string, RegExp]> = [
  ['carg', /dossiers_ver\.php$/],
  ['asis', /dossiers_ver\.php$/],
  ['ctrs', /dossiers_ver\.php$/],
  ['list', /lista_asistentes\.php$/],
];

for (const [boton, destino] of BOTONES) {
  test(`lista de actividades sg: el botón «${boton}» abre su pantalla`, async ({ page }) => {
    const inc = await abrirApp(page);
    const main = page.locator('#main');
    await abrirPantalla(page, 'frontend/actividades/controller/lista_actividades_sg.php?tipo_activ_sg=crt', 'form#seleccionados');
    await redEnCalma(page);
    const fila = main.locator('#seleccionados input[name="sel[]"]').first();
    test.skip((await fila.count()) === 0, 'no hay actividades sg en la lista');
    await marcarFila(page, fila);

    const [respuesta] = await Promise.all([
      page.waitForResponse((r) => r.request().method() === 'POST' && destino.test(new URL(r.url()).pathname), { timeout: 30_000 }),
      main.locator(`[onclick*='"#seleccionados","${boton}"']`).first().click(),
    ]);
    expect(respuesta.status(), 'la petición no se rechaza (un 302 es un hash de formulario que no coincide)').toBe(200);
    await redEnCalma(page);
    await sinErrorDeBackend(page);
    await expect(main.locator('#seleccionados input[name="sel[]"]'), 'ya no es la lista').toHaveCount(0);
    if (boton === 'ctrs') {
      await expect(main).toContainText(/centr/i);
    }
    expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  });
}
