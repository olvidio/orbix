import { expect, test } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, crearNumerario, esperarSrc, limpiar, marcaE2e, sqlDev } from '../../orbix-app';

/**
 * Subida de ficheros (fase 3.4): adjuntar a una persona un certificado recibido en PDF.
 * Crear un numerario → «adjuntar certificado» → número, fechas y el PDF → «subir pdf»
 * (`certificado_recibido_pdf_upload`, multipart) → el certificado está en la BD con el mismo
 * fichero. (Hoy no se comprueba que el fichero sea un PDF: duda 11 de la sección 6b del plan.)
 *
 * La persona es del propio test; al final se borran con SQL sus certificados, sus marcas de
 * dossier y la persona.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const APELLIDO = marcaE2e();
let idNom = '';

/** PDF mínimo válido (una página vacía). */
const PDF = Buffer.from(
  '%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n' +
    '3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n',
);

const certificados = (campos: string) => sqlDev('sv', `SELECT ${campos} FROM "${ESQUEMA_V}".e_certificados_dl WHERE id_nom = ${idNom || 0} ORDER BY id_item`);

test.afterAll(() => {
  limpiar(
    () => idNom !== '' && sqlDev('sv', `DELETE FROM "${ESQUEMA_V}".e_certificados_dl WHERE id_nom = ${idNom}`),
    () => idNom !== '' && sqlDev('sv', `DELETE FROM "${ESQUEMA_V}".d_dossiers_abiertos WHERE tabla = 'p' AND id_pau = ${idNom}`),
    () => sqlDev('sv', `DELETE FROM "${ESQUEMA_V}".p_numerarios WHERE apellido1 = '${APELLIDO}'`),
  );
});

test('certificado recibido: subir el PDF a una persona', async ({ page }) => {
  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  idNom = await crearNumerario(page, APELLIDO, ESQUEMA_V);

  const pantalla = `frontend/certificados/controller/certificado_recibido_adjuntar.php?sel[]=${idNom}%23n`;
  await abrirPantalla(page, pantalla, 'form#frm_upload');
  const form = page.locator('#main form#frm_upload');
  await form.locator('[name="certificado"]').fill('E2E 1/31');
  await form.locator('#f_certificado').fill('1/3/2031');
  await form.locator('#f_recibido').fill('5/3/2031');

  await form.locator('#fileToUpload').setInputFiles({ name: 'certificado.pdf', mimeType: 'application/pdf', buffer: PDF });
  const subida = await esperarSrc(page, 'certificado_recibido_pdf_upload', () => form.locator('input[onclick*="fnjs_upload_pdf"]').click());
  expect(subida.success, `${subida.mensaje ?? ''} ${dialogos.join(' / ')}`).toBe(true);
  expect(certificados(`certificado || '|' || f_certificado || '|' || f_recibido`)).toBe('E2E 1/31|2031-03-01|2031-03-05');
  expect(certificados('md5(documento)'), 'guarda el mismo fichero').toBe(require('node:crypto').createHash('md5').update(PDF).digest('hex'));

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
});
