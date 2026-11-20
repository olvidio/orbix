import fs from 'node:fs';
import path from 'node:path';

import { expect, test } from '@playwright/test';

import { diagnoseBackendHtml } from '../../backend-diagnostics';
import { camposFirmadosEnElCodigo, comprobarFormularios, diagnosticar, formulariosDeHtml, idSesion } from '../../form-hash-audit';
import { indexPathForE2e } from '../../grupmenu';
import { conPreferenciasDelUsuario, sePuedeAbrir } from '../../pantallas';

/**
 * «Carga sin error» de las pantallas de `frontend/` (fase 5.3): también las que no cuelgan del menú y
 * se abren desde un formulario o una lista.
 *
 * Abre cada `frontend/<modulo>/controller/*.php` con un POST sin parámetros, como hace el menú
 * (`fnjs_update_div`), y comprueba que no responde con un error de PHP (fatal, excepción sin capturar,
 * avisos), con un 5xx, ni con el aviso de «sesión no autenticada en una llamada interna» (síntoma de una
 * llamada a `/src` mal formada). Una pantalla a la que le falta un dato debe decirlo o pintarse vacía, no romperse.
 * Es el equivalente, en la capa de pantallas, del test-guardia de PHPUnit sobre los controladores de `src/`.
 *
 * No se abren: los controladores que por su nombre ejecutan una acción (guardar, eliminar, enviar…), los
 * módulos de administración de la BD y de generación de código, ni las piezas de navegación.
 *
 * `E2E_PANTALLAS=modulo` (o `modulo/fichero.php`) limita la pasada.
 */
const RAIZ = path.resolve(__dirname, '../../..');

/** Tiempo máximo por pantalla (`E2E_PANTALLAS_TIMEOUT_MS`). Sin filtros, algunos listados tardan. */
const TIEMPO_MAX_MS = Number(process.env.E2E_PANTALLAS_TIMEOUT_MS ?? 60_000);

const ERROR_PHP =
  /Fatal error|Uncaught |<b>(Warning|Notice|Deprecated)<\/b>|(^|\n)(Warning|Notice|Deprecated): |\bxdebug-error\b|Stack trace:|SQLSTATE\[|no autenticada en una (llamada|crida) interna/;

function pantallas(): string[] {
  const filtro = process.env.E2E_PANTALLAS ?? '';
  const out: string[] = [];
  for (const modulo of fs.readdirSync(path.join(RAIZ, 'frontend')).sort()) {
    const dir = path.join(RAIZ, 'frontend', modulo, 'controller');
    if (!fs.existsSync(dir)) {
      continue;
    }
    for (const fichero of fs.readdirSync(dir).sort()) {
      const rel = `${modulo}/controller/${fichero}`;
      if (!sePuedeAbrir(rel)) {
        continue;
      }
      if (filtro !== '' && !rel.includes(filtro)) {
        continue;
      }
      out.push(rel);
    }
  }
  return out;
}

test('las pantallas de frontend/ se abren sin parámetros sin error de PHP', async ({ page }) => {
  test.setTimeout(20 * 60_000);
  await page.goto(indexPathForE2e());
  await expect(page.locator('#main')).toBeAttached();
  const base = new URL('.', page.url()).href;

  const lista = pantallas();
  expect(lista.length, 'hay pantallas que abrir').toBeGreaterThan(0);

  const fallos: string[] = [];
  const sesion = await idSesion(page);
  expect(sesion, 'cookie de sesión').not.toBe('');
  const hashes = { formularios: 0, condicionales: [] as string[], fallan: [] as string[] };
  // Varias pantallas guardan «la última búsqueda» como preferencia del usuario al abrirse.
  await conPreferenciasDelUsuario(() => abrirTodas(lista, fallos));

  console.log(`pantallas abiertas: ${lista.length}; con error: ${fallos.length}`);
  // Solo informativo: sin parámetros la pantalla no está en un estado real (faltan campos que dependen
  // de ellos y los desplegables que rellena el JavaScript). Lo que se comprueba de verdad son las
  // pantallas del menú (`formularios-del-menu.spec.ts`). Con `E2E_PANTALLAS_LOG=1` se listan, para
  // revisar a mano las pantallas que no cuelgan del menú.
  console.log(`formularios firmados: ${hashes.formularios}; hash que no coincide: ${hashes.fallan.length}; coincide solo rellenando algo: ${hashes.condicionales.length}`);
  if (process.env.E2E_PANTALLAS_LOG === '1') {
    console.log(['--- condicionales', ...hashes.condicionales, '--- fallan', ...hashes.fallan].join('\n'));
  }
  expect(fallos, `\n${fallos.join('\n\n')}\n`).toEqual([]);

  async function abrirTodas(lista: string[], fallos: string[]): Promise<void> {
  for (const rel of lista) {
    let status = 0;
    let cuerpo = '';
    const t0 = Date.now();
    try {
      const res = await page.request.post(new URL(`frontend/${rel}`, base).href, {
        // Un POST no vacío sin datos: `PHPSESSID` es el parámetro que añade la interfaz y no necesita firma.
        form: { PHPSESSID: '1' },
        maxRedirects: 5,
        timeout: TIEMPO_MAX_MS,
        failOnStatusCode: false,
      });
      status = res.status();
      cuerpo = await res.text();
      if (process.env.E2E_PANTALLAS_LOG === '1') {
        console.log(rel, status, `${Date.now() - t0} ms`, cuerpo.length, cuerpo.slice(0, 90).replace(/\s+/g, ' '));
      }
    } catch (e) {
      fallos.push(`${rel}: sin respuesta (${e instanceof Error ? e.message.split('\n')[0] : String(e)})`);
      continue;
    }
    const formularios = await formulariosDeHtml(page, cuerpo);
    comprobarFormularios(formularios, sesion).forEach((r, i) => {
      hashes.formularios++;
      if (r.estado === 'condicional') {
        hashes.condicionales.push(`${rel} · ${r.nombre}: coincide si se envía ${r.con.join(', ')}`);
      } else if (r.estado === 'falla') {
        const f = { ...formularios[i], enviados: [...formularios[i].enviados, ...formularios[i].opcionales.map((o) => o.nombre)] };
        const diag = diagnosticar(f, sesion, camposFirmadosEnElCodigo(RAIZ, rel), 4) ?? diagnosticar(f, sesion, camposFirmadosEnElCodigo(RAIZ), 2);
        hashes.fallan.push(`${rel} · ${r.nombre}: ${diag ?? `sin explicación; envía [${r.enviados.join(', ')}]${r.opcionales.length > 0 ? `; opcionales [${r.opcionales.join(', ')}]` : ''}`}`);
      }
    });
    const diag = diagnoseBackendHtml(cuerpo);
    const m = ERROR_PHP.exec(cuerpo);
    if (status >= 500 || diag !== null || m !== null) {
      const donde = m === null ? 0 : Math.max(0, m.index - 80);
      const recorte = cuerpo.slice(donde, donde + 420).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
      fallos.push(`${rel}: HTTP ${status}${diag === null ? '' : ' · ' + diag} · ${recorte}`);
    }
  }
  }
});
