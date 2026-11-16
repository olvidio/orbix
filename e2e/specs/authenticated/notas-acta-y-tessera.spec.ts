import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, crearNumerario, esperarSrc, limpiar, marcaE2e, marcarFila, redEnCalma, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujos de escritura de notas que solo tenían comprobada la cápsula (`notas-capsulas`), ahora con
 * guardado real por la interfaz:
 *
 * 1. Modificar una nota desde el dossier de la persona (`persona_nota_editar`) y copiar su tessera a
 *    otra persona con el mismo primer apellido (`tessera_copiar`).
 * 2. Crear un acta (`acta_nueva`), añadirle un alumno con nota (`acta_ver_add_persona`), imprimirla
 *    (sin imprimir no se puede subir el PDF) y subir el PDF firmado (`acta_pdf_upload`): el acta queda
 *    cerrada y la dl no puede quitar el PDF (solo la región del stgr).
 *
 * Personas, notas y acta son del propio test (año 2031); al final se borran con SQL.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const DL = (ESQUEMA_V.split('-')[1] ?? 'dlbv').replace(/v$/, '');
const MARCA = marcaE2e();
const APELLIDO_TESSERA = `${MARCA}t`;
const APELLIDO_ACTA = `${MARCA}a`;
const ACTA = `${DL} 998/31`;
const ASIGNATURA_INTRO_FILOSOFIA = '1101';
const SITUACION_SUPERADA = '1';
const NIVEL_STGR_BIENIO = '1';
const T = `"${ESQUEMA_V}"`;

// Una opción que no está en un desplegable falla a los 20 s, no al agotar el tiempo del proyecto.
test.use({ actionTimeout: 20_000 });

/** PDF mínimo válido (una página vacía). */
const PDF = Buffer.from(
  '%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n' +
    '3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n',
);

const personas = (apellido: string) => `(SELECT id_nom FROM ${T}.p_numerarios WHERE apellido1 = '${apellido}')`;
const notasDe = (idNom: string, campos: string) => sqlDev('sv', `SELECT ${campos} FROM ${T}.e_notas_dl WHERE id_nom = ${idNom || 0} ORDER BY id_nivel`);

test.afterAll(() => {
  limpiar(
    ...[APELLIDO_TESSERA, APELLIDO_ACTA].flatMap((apellido) => [
      () => sqlDev('sv', `DELETE FROM ${T}.e_notas_dl WHERE id_nom IN ${personas(apellido)}`),
      () => sqlDev('sv', `DELETE FROM ${T}.d_dossiers_abiertos WHERE tabla = 'p' AND id_pau IN ${personas(apellido)}`),
    ]),
    () => sqlDev('sv', `DELETE FROM ${T}.e_actas_tribunal_dl WHERE acta = '${ACTA}'`),
    () => sqlDev('sv', `DELETE FROM ${T}.e_actas_dl WHERE acta = '${ACTA}'`),
    () => sqlDev('sv', `DELETE FROM ${T}.p_numerarios WHERE apellido1 IN ('${APELLIDO_TESSERA}', '${APELLIDO_ACTA}')`),
  );
});

test('nota: modificarla desde el dossier y copiar la tessera a otra persona', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const origen = await crearNumerario(page, APELLIDO_TESSERA, ESQUEMA_V);

  // Segunda persona con el mismo primer apellido: es la condición para ofrecerla como destino.
  const destino = await crearNumerario(page, APELLIDO_TESSERA, ESQUEMA_V, { nombre: 'Destino' });
  expect(destino, 'son dos personas distintas').not.toBe(origen);

  // --- Una nota para la persona de origen.
  const dossier = `frontend/dossiers/controller/dossiers_ver.php?pau=p&id_pau=${origen}&obj_pau=PersonaN&id_dossier=1011&permiso=3`;
  await abrirPantalla(page, dossier, '.links-insert-btn');
  await page.locator('#main .links-insert-btn').first().click();
  const form = page.locator('#main form#form_notas_de_una_persona');
  await expect(form).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await form.locator('#tipo_acta_a').check();
  await form.locator('#acta_sigla').selectOption(DL);
  await form.locator('#acta_num').fill('997/31');
  await form.locator('#id_nivel').selectOption(ASIGNATURA_INTRO_FILOSOFIA);
  await form.locator('#id_situacion').selectOption(SITUACION_SUPERADA);
  await form.locator('#f_acta').fill('15/6/2031');
  const alta = await esperarSrc(page, 'persona_nota_nueva', () => form.locator('input[onclick*="fnjs_guardar(this.form)"]').click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  await redEnCalma(page);

  // --- Modificarla: marcar la fila, «modificar», cambiar la fecha del acta y guardar.
  await abrirPantalla(page, dossier, 'input.sel', true);
  await marcarFila(page, page.locator('#main input.sel').first());
  await page.locator('#main [onclick*="fnjs_modificar(this.form)"]').first().click();
  await expect(form.locator('#f_acta')).toHaveValue('15/6/2031', { timeout: 20_000 });
  await sinErrorDeBackend(page);
  await redEnCalma(page);
  await form.locator('#f_acta').fill('20/6/2031');
  const cambio = await esperarSrc(page, 'persona_nota_editar', () => form.locator('input[onclick*="fnjs_guardar(this.form)"]').click());
  expect(cambio.success, `${cambio.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(notasDe(origen, `id_nivel || '|' || id_situacion || '|' || acta || '|' || f_acta`), 'la nota tiene la fecha nueva y nada más cambia').toBe(
    `${ASIGNATURA_INTRO_FILOSOFIA}|${SITUACION_SUPERADA}|${DL} 997/31|2031-06-20`,
  );
  await redEnCalma(page);

  // --- Copiar la tessera a la otra persona.
  await abrirPantalla(page, `frontend/notas/controller/tessera_copiar_select.php?sel[]=${origen}%23n`, 'form#frm_sin_nombre');
  const copia = page.locator('#main form#frm_sin_nombre');
  await expect(copia.locator('select[name="id_nom_dst"] option[value]:not([value=""])'), 'solo se ofrece la otra persona del mismo apellido').toHaveCount(1);
  await copia.locator('select[name="id_nom_dst"]').selectOption(destino);
  const copiado = await esperarSrc(page, 'tessera_copiar', () => copia.locator('#copiar').click());
  expect(copiado.success, copiado.mensaje).toBe(true);
  expect(notasDe(destino, `id_nivel || '|' || id_situacion || '|' || acta || '|' || f_acta`), 'la persona de destino tiene la misma nota').toBe(
    notasDe(origen, `id_nivel || '|' || id_situacion || '|' || acta || '|' || f_acta`),
  );
  expect(notasDe(origen, 'acta'), 'la de origen conserva la suya').toBe(`${DL} 997/31`);

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});

test('acta: crearla, añadir un alumno con nota, imprimirla y subir el PDF firmado', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  // Con nivel de estudios «bienio»: «añadir alumno al acta» no ofrece a quien no tiene nivel (duda 24 del plan).
  const alumno = await crearNumerario(page, APELLIDO_ACTA, ESQUEMA_V, { nivelStgr: NIVEL_STGR_BIENIO });
  const acta = (campos: string) => sqlDev('sv', `SELECT ${campos} FROM ${T}.e_actas_dl WHERE acta = '${ACTA}'`);
  const verActa = `frontend/notas/controller/acta_ver.php?acta=${encodeURIComponent(ACTA)}`;

  // --- Acta nueva: número, fecha y asignatura (autocompletar).
  await abrirPantalla(page, 'frontend/notas/controller/acta_ver.php?mod=nueva', 'form#frm_acta');
  const form = page.locator('#main form#frm_acta');
  await form.locator('#acta_num').fill('998/31');
  await form.locator('#f_acta').fill('15/6/2031');
  await page.keyboard.press('Escape'); // cierra el calendario
  await form.locator('#name_asignatura').pressSequentially('Introd', { delay: 60 });
  const sugerencia = page.locator('ul.ui-autocomplete li').first();
  await expect(sugerencia, 'el autocompletar ofrece asignaturas').toBeVisible({ timeout: 15_000 });
  await sugerencia.click();
  const idAsignatura = await form.locator('#id_asignatura').inputValue();
  expect(idAsignatura, 'se ha elegido una asignatura').toMatch(/^[1-9]\d*$/);
  const alta = await esperarSrc(page, 'acta_nueva', () => form.locator('#g1').click());
  expect(alta.success, `${alta.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(acta(`id_asignatura || '|' || f_acta || '|' || (pdf IS NULL OR length(pdf) = 0)`)).toBe(`${idAsignatura}|2031-06-15|true`);
  await redEnCalma(page);

  // --- Añadir al alumno con su nota.
  await abrirPantalla(page, verActa, 'form#frm_add_persona');
  const anadir = page.locator('#main form#frm_add_persona');
  await anadir.locator('select[name="id_nom"]').selectOption(alumno);
  await anadir.locator('[name="nota_num"]').fill('8');
  const nota = await esperarSrc(page, 'acta_ver_add_persona', () => anadir.locator('input[onclick*="fnjs_acta_add_persona"]').click());
  expect(nota.success, nota.mensaje).toBe(true);
  expect(notasDe(alumno, `id_asignatura || '|' || acta || '|' || nota_num::numeric(4,1) || '|' || f_acta`), 'la nota queda en el dossier del alumno, con el acta').toBe(`${idAsignatura}|${ACTA}|8.0|2031-06-15`);
  await redEnCalma(page);

  // --- Con cambios sin imprimir no se puede subir el PDF.
  await abrirPantalla(page, verActa, 'form#frm_upload');
  await expect(page.locator('#main #acta_notas_listado'), 'el alumno sale en el listado del acta').toContainText(new RegExp(`${APELLIDO_ACTA}.*\\(8/10\\)`, 'is'));
  await expect(page.locator('#main #frm_upload #fileToUpload'), 'sin imprimir, la subida está desactivada').toBeDisabled();

  // --- Imprimir (anota la huella del contenido impreso) y subir el PDF firmado.
  await abrirPantalla(page, `frontend/notas/controller/acta_imprimir.php?acta=${encodeURIComponent(ACTA)}`, 'table, div');
  await redEnCalma(page);
  await abrirPantalla(page, verActa, 'form#frm_upload');
  const subir = page.locator('#main form#frm_upload');
  await expect(subir.locator('#fileToUpload'), 'una vez impresa, se puede subir').toBeEnabled();
  // Un fichero que no es PDF se rechaza (duda 25 del plan) y el acta sigue sin PDF.
  await subir.locator('#fileToUpload').setInputFiles({ name: 'falso.pdf', mimeType: 'application/pdf', buffer: Buffer.from('no soy un pdf') });
  const falso = await esperarSrc(page, 'acta_pdf_upload', () => subir.locator('input[onclick*="fnjs_upload_pdf"]').click());
  expect(falso.success, 'rechaza lo que no es PDF').toBe(false);
  expect(falso.mensaje).toContain('falso.pdf');
  expect(acta('pdf IS NULL OR length(pdf) = 0'), 'no se ha guardado nada').toBe('t');
  await subir.locator('#fileToUpload').setInputFiles({ name: 'acta.pdf', mimeType: 'application/pdf', buffer: PDF });
  const subida = await esperarSrc(page, 'acta_pdf_upload', () => subir.locator('input[onclick*="fnjs_upload_pdf"]').click());
  expect(subida.success, subida.mensaje).toBe(true);
  expect(acta('md5(pdf)'), 'guarda el mismo fichero').toBe(require('node:crypto').createHash('md5').update(PDF).digest('hex'));
  await redEnCalma(page);

  // --- El acta firmada queda cerrada; la dl no puede quitar el PDF (solo la región del stgr).
  await abrirPantalla(page, verActa, 'form#frm_acta');
  const main = page.locator('#main');
  await expect(main.locator('#frm_acta #f_acta')).toHaveAttribute('readonly', /.*/);
  await expect(main.locator('#frm_upload'), 'ya no se ofrece subir otro PDF').toHaveCount(0);
  await expect(main.locator('#frm_add_persona'), 'ni añadir alumnos').toHaveCount(0);
  await expect(main.locator('input[onclick*="window.open"]'), 'se ofrece descargar el PDF').toHaveCount(1);
  await expect(main.locator('input[onclick*="fnjs_eliminar_pdf"]'), 'la dl no puede quitar el PDF').toHaveCount(0);

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
