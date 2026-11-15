import { expect, test, type Page } from '@playwright/test';
import { abrirApp, abrirPantalla, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Los botones «petición ca» / «petición crt» de la lista de personas solo se ofrecen a los numerarios
 * y agregados de la dl, no a las personas de paso: para ellas la pantalla de peticiones no tiene
 * actividades candidatas (duda 19 del plan de tests). Solo lectura: busca y mira los botones.
 *
 * Cubre el camino de los menús (`tabla=p_de_paso`). El otro (`p_de_paso_ex`: un usuario con rol de
 * persona que es de paso) no se puede abrir con el usuario de E2E.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';

/** Busca por primer apellido en la pantalla de búsqueda y devuelve el HTML de la lista resultante. */
async function listaDe(page: Page, parametros: string, apellido: string): Promise<string> {
  await abrirPantalla(page, `frontend/personas/controller/personas_que.php?${parametros}`, '#frm_personas_que');
  await page.fill('#frm_personas_que #apellido1', apellido);
  await page.click('#btn_ok');
  const main = page.locator('#main');
  await expect(main.locator('#seleccionados')).toBeAttached({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  return main.innerHTML();
}

test('«petición ca / crt» se ofrece a numerarios y no a personas de paso', async ({ page }) => {
  // Módulo de plazas instalado en la dl: sin él no hay botones para nadie.
  const numerario = sqlDev('sv', `SELECT apellido1 FROM "${ESQUEMA_V}".p_numerarios WHERE situacion = 'A' AND apellido1 <> '' ORDER BY id_nom LIMIT 1`);
  const dePaso = sqlDev('sv', `SELECT apellido1 FROM restov.p_de_paso_ex WHERE id_tabla = 'pn' AND situacion = 'A' AND apellido1 <> '' ORDER BY id_nom LIMIT 1`);
  test.skip(numerario === '' || dePaso === '', 'hace falta un numerario de la dl y una persona de paso');

  const inc = await abrirApp(page);

  const htmlN = await listaDe(page, 'tabla=p_numerarios&tipo=persona', numerario);
  test.skip(!htmlN.includes('fnjs_posibles_ca') && !htmlN.includes('fnjs_peticion_activ'), 'la dl no tiene instalado el módulo de plazas');
  expect(htmlN, 'los numerarios tienen los botones de petición').toContain('fnjs_peticion_activ');

  const htmlPaso = await listaDe(page, 'tabla=p_de_paso&na=n&tipo=persona', dePaso);
  expect(htmlPaso, 'la lista de personas de paso tiene sus botones').toContain('fnjs_dossiers');
  expect(htmlPaso, 'las personas de paso no tienen botones de petición').not.toContain('fnjs_peticion_activ');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
});
