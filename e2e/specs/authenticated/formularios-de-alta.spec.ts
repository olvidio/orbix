import path from 'node:path';

import { expect, test } from '@playwright/test';

import { camposFirmadosEnElCodigo, comprobarFormularios, diagnosticar, formulariosDePagina, idSesion } from '../../form-hash-audit';
import { abrirApp, redEnCalma } from '../../orbix-app';

/**
 * Hashes de los formularios que una pantalla abre en una ventana (alta, valor por defecto…) y que
 * `formularios-del-menu.spec.ts` no ve porque no están pintados al abrir la entrada del menú (fase 6.4).
 *
 * Abre la pantalla, llama a la función de la vista que pinta el formulario y comprueba que los campos
 * que enviaría son los que firma (ver `e2e/form-hash-audit.ts`). Solo lectura: no guarda nada.
 *
 * Los formularios de alta que incrustan los desplegables del tipo de actividad llevan un campo oculto
 * `extendida`; si no está en `setCamposForm()` ni en `setCamposNo()`, guardar se rechaza sin mensaje
 * (302 al inicio y sesión cerrada). Pasaba en la relación tarifa–tipo de actividad y en las dos
 * contribuciones de la pasarela.
 */
const RAIZ = path.resolve(__dirname, '../../..');

/** [pantalla (`modulo/controller/x.php`), función global de la vista que abre el formulario, sus argumentos] */
const FORMULARIOS: Array<[string, string, unknown[]]> = [
  ['actividadtarifas/controller/tarifa_tipo_actividad.php', 'fnjs_modificar', ['nuevo']],
  ['pasarela/controller/contribucion_no_duerme_lista.php', 'fnjs_nuevo', []],
  ['pasarela/controller/contribucion_no_duerme_lista.php', 'fnjs_modificar_default', []],
  ['pasarela/controller/contribucion_reserva_lista.php', 'fnjs_nuevo', []],
  ['pasarela/controller/contribucion_reserva_lista.php', 'fnjs_modificar_default', []],
  ['pasarela/controller/activacion_lista.php', 'fnjs_nuevo_activacion', []],
  ['pasarela/controller/activacion_lista.php', 'fnjs_modificar_activacion_default', []],
];

test('los formularios de alta envían los campos que firman', async ({ page }) => {
  await abrirApp(page);
  page.on('dialog', (d) => d.dismiss().catch(() => undefined));
  const sesion = await idSesion(page);
  expect(sesion, 'cookie de sesión').not.toBe('');

  const fallan: string[] = [];
  let comprobados = 0;
  for (const [rel, funcion, args] of FORMULARIOS) {
    const etiqueta = `${rel} · ${funcion}(${args.map((a) => JSON.stringify(a)).join(', ')})`;
    const ruta = `frontend/${rel}`;
    await Promise.all([
      page.waitForResponse((r) => r.request().method() === 'POST' && new URL(r.url()).pathname.endsWith(ruta), { timeout: 30_000 }),
      page.evaluate((p) => {
        // @ts-expect-error función global de Orbix (scripts/index.js.php)
        fnjs_link_submenu(p, '');
      }, ruta),
    ]);
    await redEnCalma(page);
    const antes = (await formulariosDePagina(page)).length;
    const existe = await page.evaluate(
      ([f, a]) => {
        const fn = (window as unknown as Record<string, unknown>)[f as string];
        if (typeof fn !== 'function') {
          return false;
        }
        fn(...(a as unknown[]));
        return true;
      },
      [funcion, args] as const,
    );
    expect(existe, `${etiqueta}: la vista define la función`).toBe(true);
    await expect.poll(async () => (await formulariosDePagina(page)).length, { message: `${etiqueta}: pinta un formulario firmado`, timeout: 20_000 }).toBeGreaterThan(antes);
    await redEnCalma(page);

    const leidos = (await formulariosDePagina(page)).filter((f) => f.visible);
    comprobarFormularios(leidos, sesion).forEach((r, i) => {
      comprobados++;
      if (r.estado === 'falla') {
        const f = { ...leidos[i], enviados: [...leidos[i].enviados, ...leidos[i].opcionales.map((o) => o.nombre)] };
        const diag = diagnosticar(f, sesion, camposFirmadosEnElCodigo(RAIZ, rel), 4) ?? diagnosticar(f, sesion, camposFirmadosEnElCodigo(RAIZ), 2);
        fallan.push(`${etiqueta} · ${r.nombre}: ${diag ?? `sin explicación; envía [${r.enviados.join(', ')}]`}`);
      }
    });
  }
  expect(comprobados, 'se han comprobado formularios').toBeGreaterThanOrEqual(FORMULARIOS.length);
  expect(fallan, `formularios cuyo hash no coincide con los campos que envían:\n${fallan.join('\n')}\n`).toEqual([]);
});
