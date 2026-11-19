import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, esperarSrc, limpiar, marcaE2e, sqlDev } from '../../orbix-app';

/**
 * «Preguntar a la documentación» anota cada pregunta (sin el usuario) y deja decir si la respuesta
 * sirvió; un administrador revisa las anotadas en `frontend/shared/controller/ayuda_consultas_lista.php`.
 *
 * Recorre: preguntar desde una pantalla → «No» → comentario → la pregunta sale en la revisión →
 * «Resuelta». La pregunta lleva una marca propia y sus filas se borran al final.
 */
const marca = marcaE2e();
const PREGUNTA = `zzqx ${marca} como aviso de que la ayuda no me responde`;
const DONDE = `pregunta = '${PREGUNTA}'`;
const fila = (columnas: string) => sqlDev('comun', `SELECT ${columnas} FROM public.ayuda_consultas WHERE ${DONDE}`);

test.afterAll(() => {
  limpiar(() => sqlDev('comun', `DELETE FROM public.ayuda_consultas WHERE pregunta LIKE 'zzqx ${marca} %'`));
});

test('ayuda: la pregunta queda anotada, se valora y un administrador la revisa', async ({ page }) => {
  await abrirApp(page);
  const main = page.locator('#main');

  // El panel del interrogante abre la ayuda diciendo desde qué pantalla se pregunta.
  await abrirPantalla(
    page,
    'frontend/shared/controller/ayuda_preguntar.php?pantalla=' + encodeURIComponent('frontend/notas/controller/acta_ver.php'),
    '#ayuda_pregunta',
  );
  await main.locator('#ayuda_pregunta').fill(PREGUNTA);
  const rta = await esperarSrc(page, 'shared/ayuda_preguntar', () => main.locator('form.ayuda-form button[type="submit"]').click());
  expect(rta.success, rta.mensaje).toBe(true);
  // La respuesta se pinta: con un texto mal codificado en un resultado, `data` llegaba vacío y no salía nada.
  await expect(main.locator('#ayuda_preguntar_resultados .ayuda-respuesta')).toContainText(/\S/);
  await expect(main.locator('#ayuda_preguntar_resultados .ayuda-hit').first()).toBeVisible();

  // Anotada sin valorar, con la pantalla y la delegación; ninguna columna guarda el usuario.
  expect(fila(`count(*)`)).toBe('1');
  expect(fila(`coalesce(pantalla, '') || '|' || coalesce(util::text, 'sin valorar') || '|' || estado`)).toBe(
    'frontend/notas/controller/acta_ver.php|sin valorar|1',
  );
  expect(fila(`coalesce(esquema, '')`)).not.toBe('');
  const columnas = sqlDev('comun', `SELECT string_agg(column_name, ',') FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'ayuda_consultas'`);
  expect(columnas).not.toMatch(/usuario|username|id_usuario/);

  // «No»: queda marcada al momento; el comentario se añade después.
  await expect(main.locator('#ayuda_valorar_no')).toBeVisible();
  const no = await esperarSrc(page, 'shared/ayuda_consulta_valorar', () => main.locator('#ayuda_valorar_no').click());
  expect(no.success, no.mensaje).toBe(true);
  expect(fila(`util::text`)).toBe('false');

  await main.locator('#ayuda_valorar_comentario').fill(`buscaba ${marca}`);
  const comentario = await esperarSrc(page, 'shared/ayuda_consulta_valorar', () => main.locator('#ayuda_valorar_enviar').click());
  expect(comentario.success, comentario.mensaje).toBe(true);
  expect(fila(`comentario`)).toBe(`buscaba ${marca}`);
  await expect(main.locator('#ayuda_valorar')).toContainText(/\S/);
  await expect(main.locator('#ayuda_valorar_enviar')).toHaveCount(0);

  // Revisión (el usuario de E2E es administrador): la pregunta sale con su comentario y se resuelve.
  await abrirPantalla(page, 'frontend/shared/controller/ayuda_consultas_lista.php', '#ayuda_consultas_tabla');
  const tr = main.locator('#ayuda_consultas_tabla tbody tr', { hasText: PREGUNTA });
  await expect(tr).toHaveCount(1);
  await expect(tr).toContainText(`buscaba ${marca}`);
  await expect(main.locator('#ayuda_consultas_tsv')).toHaveValue(new RegExp(`^${PREGUNTA}\\t`, 'm'));

  const resuelta = await esperarSrc(page, 'shared/ayuda_consultas_estado_update', () => tr.locator('button[name="resolver"]').click());
  expect(resuelta.success, resuelta.mensaje).toBe(true);
  expect(fila(`estado`)).toBe('2');
  // La lista se recarga y la pregunta ya no está entre las pendientes.
  await expect(main.locator('#ayuda_consultas_tabla tbody tr', { hasText: PREGUNTA })).toHaveCount(0);
});
