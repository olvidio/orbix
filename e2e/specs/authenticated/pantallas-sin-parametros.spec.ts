import fs from 'node:fs';
import path from 'node:path';

import { expect, test } from '@playwright/test';

import { diagnoseBackendHtml } from '../../backend-diagnostics';
import { indexPathForE2e } from '../../grupmenu';
import { sqlDev } from '../../orbix-app';

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
const T = `"${process.env.E2E_ESQUEMA ?? 'H-dlbv'}"`;

const PATRON_ACCION =
  /(update|guardar|eliminar|delete|borrar|nuevo|nueva|crear|aprobar|importar|exportar|enviar|resincronizar|aplicar|upload|subir|download|descargar|mover|copiar|duplicar|publicar|cambiar|marcar|asignar|quitar|add|del|insert|generar|grabar|recuperar|restablecer|login|session|logout|ejecutar|absorber|renombrar|trasladar|cerrar|abrir|anular|firmar|imprimir|sincro|calcular|actualizar|reset|bytea|mpdf|pdf)/i;

/** Módulos que no se abren: DDL sobre la BD y generación de ficheros de código. */
const MODULOS_EXCLUIDOS = new Set(['devel_db_admin', 'devel_codegen']);

/** Controladores concretos que no se abren, con el motivo. */
const EXCLUIDOS: Record<string, string> = {
  'shared/controller/nav_atras.php': 'navegación: cambia la pila de la sesión',
  'shared/controller/nav_state.php': 'navegación: cambia la pila de la sesión',
  'shared/controller/src_ajax.php': 'proxy de /src: no es una pantalla',
  'actividadestudios/controller/matriculas_pendientes.php': 'borra las matrículas pendientes de personas que ya no están (duda 18)',
};

/** Tiempo máximo por pantalla (`E2E_PANTALLAS_TIMEOUT_MS`). Sin filtros, algunos listados tardan. */
const TIEMPO_MAX_MS = Number(process.env.E2E_PANTALLAS_TIMEOUT_MS ?? 60_000);

const ERROR_PHP =
  /Fatal error|Uncaught |<b>(Warning|Notice|Deprecated)<\/b>|(^|\n)(Warning|Notice|Deprecated): |\bxdebug-error\b|Stack trace:|SQLSTATE\[|no autenticada en una (llamada|crida) interna/;

function pantallas(): string[] {
  const filtro = process.env.E2E_PANTALLAS ?? '';
  const out: string[] = [];
  for (const modulo of fs.readdirSync(path.join(RAIZ, 'frontend')).sort()) {
    const dir = path.join(RAIZ, 'frontend', modulo, 'controller');
    if (MODULOS_EXCLUIDOS.has(modulo) || !fs.existsSync(dir)) {
      continue;
    }
    for (const fichero of fs.readdirSync(dir).sort()) {
      const rel = `${modulo}/controller/${fichero}`;
      if (!fichero.endsWith('.php') || PATRON_ACCION.test(fichero) || rel in EXCLUIDOS) {
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

  // Varias pantallas guardan «la última búsqueda» como preferencia del usuario al abrirse: se guardan
  // las preferencias del usuario de E2E (un usuario real de la BD de desarrollo) y se reponen al acabar.
  const idUsuario = sqlDev('sv-e', `SELECT id_usuario FROM ${T}.aux_usuarios WHERE usuario = '${(process.env.E2E_USER ?? '').replace(/'/g, "''")}'`);
  expect(idUsuario, 'el usuario de E2E está en aux_usuarios').toMatch(/^\d+$/);
  sqlDev('sv-e', `DROP TABLE IF EXISTS public.zz_e2e_preferencias; CREATE TABLE public.zz_e2e_preferencias AS SELECT * FROM ${T}.web_preferencias WHERE id_usuario = ${idUsuario}`);

  const fallos: string[] = [];
  try {
    await abrirTodas(lista, fallos);
  } finally {
    sqlDev('sv-e', `DELETE FROM ${T}.web_preferencias WHERE id_usuario = ${idUsuario}; INSERT INTO ${T}.web_preferencias SELECT * FROM public.zz_e2e_preferencias; DROP TABLE public.zz_e2e_preferencias`);
  }

  console.log(`pantallas abiertas: ${lista.length}; con error: ${fallos.length}`);
  expect(fallos, `\n${fallos.join('\n\n')}\n`).toEqual([]);

  async function abrirTodas(lista: string[], fallos: string[]): Promise<void> {
  for (const rel of lista) {
    let status = 0;
    let cuerpo = '';
    const t0 = Date.now();
    try {
      const res = await page.request.post(new URL(`frontend/${rel}`, base).href, {
        form: { e2e_sin_parametros: '' },
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
