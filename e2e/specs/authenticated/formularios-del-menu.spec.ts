import path from 'node:path';

import { expect, test } from '@playwright/test';

import { camposFirmadosEnElCodigo, comprobarFormularios, diagnosticar, formulariosDePagina, idSesion } from '../../form-hash-audit';
import { abrirApp, redEnCalma, sqlDev } from '../../orbix-app';
import { conPreferenciasDelUsuario, sePuedeAbrir } from '../../pantallas';

/**
 * Hashes de formulario (`HashF::getCamposHtml()`) de las pantallas del menú (fase 6.4).
 *
 * Abre en `#main` cada entrada de menú de la BD (todas las plantillas y grupos: url + parámetros
 * distintos), como hace el menú, y comprueba en cada formulario firmado que los campos que enviaría
 * tal como está pintado son los que firma su `h` (ver `e2e/form-hash-audit.ts`). Si no coinciden, al
 * enviarlo la aplicación rechaza la petición sin mensaje (302 al inicio y sesión cerrada).
 *
 * Encontró tres pantallas en las que pulsar el botón cerraba la sesión: «resumen económico» de casas
 * (`casa_ec`, sin elegir casa; `casas_resumen`, siempre) y los botones de la lista de actividades sg.
 *
 * Solo lectura: no pulsa nada. No se abren las entradas que ejecutan una acción ni las de
 * administración de la BD (`e2e/pantallas.ts`).
 *
 * `E2E_PANTALLAS=texto` limita la pasada a las entradas cuya url o parámetros lo contienen.
 */
const RAIZ = path.resolve(__dirname, '../../..');

/**
 * Formularios que no se envían enteros: `pantalla · formulario` => motivo. La vista monta los
 * parámetros de su petición con otro hash (`linkSinVal`, que comprueba `audit_hash_campos.php`).
 * No añadir sin haber comprobado en la vista que ningún JavaScript hace `serialize()` del formulario.
 */
const NO_SE_ENVIAN: Record<string, string> = {
  'misas/controller/modificar_plan_de_misas.php · frm_zona_ctr': 'solo agrupa los desplegables; `fnjs_ver_cuadricula_zona` envía sus valores con `h_zona_periodo`',
  'misas/controller/preparar_plan_de_misas.php · frm_nuevo_periodo': 'solo agrupa los desplegables; las acciones envían sus valores con su propio hash',
  'misas/controller/ver_plan_de_misas.php · frm_zona_ctr': 'solo agrupa los desplegables; `fnjs_ver_cuadricula_zona` envía sus valores con `h_zona_periodo`',
};

/** Campos que el JavaScript de la vista añade a `serialize()` al enviar: `pantalla · formulario` => campos. */
const ANADIDOS_POR_JS: Record<string, string[]> = {
  'casas/controller/casa_ec.php · seleccion': ['cdc_sel', 'que'], // `fnjs_ver`: serialize() + '&cdc_sel=9&que=get'
};

function entradasDelMenu(): { rel: string; parametros: string }[] {
  const filas = sqlDev(
    'comun',
    `SELECT DISTINCT m.url || '|' || coalesce(r.parametros, '') FROM public.ref_menus r JOIN public.aux_metamenus m USING (id_metamenu) WHERE m.url LIKE 'frontend/%' ORDER BY 1`,
  );
  const filtro = process.env.E2E_PANTALLAS ?? '';
  return filas
    .split('\n')
    .filter((f) => f !== '' && (filtro === '' || f.includes(filtro)))
    .map((f) => {
      const [url, parametros] = [f.slice(0, f.indexOf('|')), f.slice(f.indexOf('|') + 1)];
      return { rel: url.replace(/^frontend\//, ''), parametros };
    })
    .filter((e) => sePuedeAbrir(e.rel));
}

test('los formularios de las pantallas del menú envían los campos que firman', async ({ page }) => {
  test.setTimeout(30 * 60_000);
  const entradas = entradasDelMenu();
  expect(entradas.length, 'hay entradas de menú que abrir').toBeGreaterThan(0);

  await abrirApp(page);
  page.on('dialog', (d) => d.dismiss().catch(() => undefined));
  const sesion = await idSesion(page);
  expect(sesion, 'cookie de sesión').not.toBe('');

  const fallan: string[] = [];
  const condicionales: string[] = [];
  const sinAbrir: string[] = [];
  let formularios = 0;

  await conPreferenciasDelUsuario(async () => {
    for (const { rel, parametros } of entradas) {
      const ruta = `frontend/${rel}`;
      const etiqueta = `${rel}${parametros === '' ? '' : '?' + parametros}`;
      try {
        await Promise.all([
          page.waitForResponse((r) => r.request().method() === 'POST' && new URL(r.url()).pathname.endsWith(ruta), { timeout: 60_000 }),
          page.evaluate(
            ([p, q]) => {
              // @ts-expect-error función global de Orbix (scripts/index.js.php)
              fnjs_link_submenu(p, q);
            },
            [ruta, parametros],
          ),
        ]);
        // Desplegables y rejillas que la pantalla carga a continuación.
        await redEnCalma(page);
      } catch (e) {
        sinAbrir.push(`${etiqueta}: ${e instanceof Error ? e.message.split('\n')[0] : String(e)}`);
        continue;
      }
      // Los formularios ocultos (p. ej. la búsqueda avanzada mientras se ve la simple) no se pueden enviar.
      const leidos = (await formulariosDePagina(page)).filter((f) => f.visible && !(`${rel} · ${f.nombre}` in NO_SE_ENVIAN));
      comprobarFormularios(leidos, sesion, (f) => ANADIDOS_POR_JS[`${rel} · ${f.nombre}`] ?? []).forEach((r, i) => {
        formularios++;
        if (r.estado === 'condicional') {
          condicionales.push(`${etiqueta} · ${r.nombre}: coincide si se envía ${r.con.join(', ')}`);
        } else if (r.estado === 'falla') {
          const f = { ...leidos[i], enviados: [...leidos[i].enviados, ...leidos[i].opcionales.map((o) => o.nombre)] };
          const diag = diagnosticar(f, sesion, camposFirmadosEnElCodigo(RAIZ, rel), 4) ?? diagnosticar(f, sesion, camposFirmadosEnElCodigo(RAIZ), 2);
          fallan.push(`${etiqueta} · ${r.nombre}: ${diag ?? `sin explicación; envía [${r.enviados.join(', ')}]; opcionales [${r.opcionales.join(', ')}]`}`);
        }
      });
    }
  });

  console.log(`entradas de menú abiertas: ${entradas.length - sinAbrir.length} de ${entradas.length}; formularios firmados: ${formularios}; no coinciden: ${fallan.length}; coinciden solo rellenando algo: ${condicionales.length}`);
  if (process.env.E2E_PANTALLAS_LOG === '1') {
    console.log(['--- sin abrir', ...sinAbrir, '--- condicionales', ...condicionales].join('\n'));
  }
  expect(sinAbrir, `entradas que no han respondido:\n${sinAbrir.join('\n')}\n`).toEqual([]);
  expect(fallan, `formularios cuyo hash no coincide con los campos que envían:\n${fallan.join('\n')}\n`).toEqual([]);
});
