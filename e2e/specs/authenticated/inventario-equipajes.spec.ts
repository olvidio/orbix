import { expect, test, type Locator } from '@playwright/test';
import { abrirApp, abrirPantalla, aceptarDialogos, esperarSrc, limpiar, marcaE2e, sinErrorDeBackend, sqlDev } from '../../orbix-app';

/**
 * Flujo de escritura de inventario (fase 5.3), por la interfaz:
 * «hacer equipajes» → periodo → casa con actividades → marcar una actividad → nombre del equipaje →
 * guardar (`equipajes_nuevo_guardar`) → verlo → «nueva valija» de un lugar → «seleccionar»
 * (`equipajes_update_grupo`) → añadir un documento libre (`equipajes_add_doc`) → quitarlo
 * (`equipajes_del_doc`) → cambiar la cabecera del listado (`equipajes_texto_listado_guardar`) →
 * eliminar la valija (`equipajes_eliminar_grupo`) → eliminar el equipaje (`equipajes_eliminar`).
 *
 * El equipaje solo apunta a la actividad (no la modifica) y a documentos que ya existen. Al final se
 * borra con SQL cualquier equipaje con la marca del test, con sus valijas y los documentos puestos en ellas.
 */
const ESQUEMA_V = process.env.E2E_ESQUEMA ?? 'H-dlbv';
const NOMBRE = marcaE2e() + ' equipaje';
const T = `"${ESQUEMA_V}"`;
const IDS = `(SELECT id_equipaje FROM ${T}.i_equipajes_dl WHERE nom_equipaje = '${NOMBRE}')`;
const EGMS = `(SELECT id_item FROM ${T}.i_egm_dl WHERE id_equipaje IN ${IDS})`;

const sql = (consulta: string) => sqlDev('sv', consulta);
/** Valores no vacíos de las opciones de un desplegable. */
const opciones = (select: Locator) => select.locator('option').evaluateAll((os) => os.map((o) => (o as HTMLOptionElement).value).filter((v) => v !== ''));

test.afterAll(() => {
  limpiar(
    () => sql(`DELETE FROM ${T}.i_whereis_dl WHERE id_item_egm IN ${EGMS}`),
    () => sql(`DELETE FROM ${T}.i_egm_dl WHERE id_equipaje IN ${IDS}`),
    () => sql(`DELETE FROM ${T}.i_equipajes_dl WHERE nom_equipaje = '${NOMBRE}'`),
  );
});

test('equipaje: alta, valija, documentos, texto del listado y borrado', async ({ page }) => {
  // Las valijas salen de los lugares del almacén, que es un parámetro de configuración de la dl.
  const almacen = sqlDev('comun', `SELECT valor FROM "${ESQUEMA_V.replace(/v$/, '')}".x_config_schema WHERE parametro = 'inventario_ubi_almacen'`);
  test.skip(!/^[1-9]\d*$/.test(almacen), 'falta el parámetro de configuración «inventario_ubi_almacen» (almacén de inventario) en la dl');

  const inc = await abrirApp(page);
  const dialogos = aceptarDialogos(page);
  const main = page.locator('#main');

  // --- Alta: periodo → casa → actividad → nombre
  await abrirPantalla(page, 'frontend/inventario/controller/equipajes_nuevo.php', 'form#seleccion');
  await main.locator('#periodo').selectOption('otro');
  await main.locator('#empiezamin').fill('1/1/2027');
  await main.locator('#empiezamax').fill('31/12/2027');
  await main.locator('input[onclick*="fnjs_ver_casas"]').click();
  const casas = main.locator('#exportar select#id_cdc');
  await expect(casas).toBeVisible({ timeout: 20_000 });
  const idCasa = (await opciones(casas))[0];
  expect(idCasa, 'hay alguna casa con actividades en 2027').toBeTruthy();
  await casas.selectOption(idCasa!);
  const actividad = main.locator('#frm_sel_activ input[name="sel[]"]').first();
  await expect(actividad).toBeAttached({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await actividad.click({ force: true });
  await main.locator('#frm_sel_activ [onclick*="fnjs_nombrar_equipaje"]').first().click();
  const nombre = main.locator('#frm_equipaje [name="nom_equipaje"]');
  await expect(nombre).toBeVisible({ timeout: 20_000 });
  await nombre.fill(NOMBRE);
  const alta = await esperarSrc(page, 'equipajes_nuevo_guardar', () => main.locator('#frm_equipaje input[onclick*="fnjs_guardar"]').click());
  expect(alta.success, alta.mensaje).toBe(true);
  const idEquipaje = sql(`SELECT id_equipaje FROM ${T}.i_equipajes_dl WHERE nom_equipaje = '${NOMBRE}'`);
  expect(idEquipaje, 'el equipaje está en la BD').toMatch(/^\d+$/);

  // --- Verlo (tras guardar se abre la lista de equipajes) y crear una valija
  await expect(main.locator('select#id_equipaje')).toBeVisible({ timeout: 20_000 });
  await abrirPantalla(page, 'frontend/inventario/controller/equipajes_ver.php?filtro=tot', 'select#id_equipaje');
  await main.locator('select#id_equipaje').selectOption(idEquipaje);
  const nuevaValija = main.locator('#form_maletas input[onclick*="fnjs_nuevo_grupo"]');
  await expect(nuevaValija).toBeVisible({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await nuevaValija.click();
  const lugares = main.locator('select#sel_grupo_1');
  await expect(lugares).toBeVisible({ timeout: 20_000 });
  // El lugar con menos documentos (pero alguno): la ventana de «quitar» no hace scroll y con una
  // valija de cientos de documentos sus botones quedan fuera de la pantalla.
  const posibles = (await opciones(lugares)).filter((v) => /^\d+$/.test(v) && v !== '1');
  expect(posibles.length, 'hay algún lugar para la valija').toBeGreaterThan(0);
  const idLugar = sql(`SELECT id_lugar FROM ${T}.i_documentos_dl WHERE id_lugar IN (${posibles.join(',')}) GROUP BY id_lugar ORDER BY count(*), id_lugar LIMIT 1`);
  expect(idLugar, 'algún lugar tiene documentos').toMatch(/^\d+$/);
  await lugares.selectOption(idLugar);
  const seleccionar = main.locator('#form_1 [onclick*="fnjs_update_grupo(1)"]').first();
  await expect(seleccionar).toBeVisible({ timeout: 20_000 });
  const valija = await esperarSrc(page, 'equipajes_update_grupo', () => seleccionar.click());
  expect(valija.success, valija.mensaje).toBe(true);
  const idEgm = sql(`SELECT id_item FROM ${T}.i_egm_dl WHERE id_equipaje = ${idEquipaje} AND id_grupo = 1`);
  expect(idEgm, 'la valija está en la BD').toMatch(/^\d+$/);
  const enValija = () => sql(`SELECT count(*) FROM ${T}.i_whereis_dl WHERE id_item_egm = ${idEgm}`);
  const alCrear = Number(enValija());

  // --- Añadir un documento libre
  const anyadir = main.locator('#form_docs_grupo_1 input[onclick*="fnjs_modificar_form_add"]');
  await expect(anyadir).toBeVisible({ timeout: 20_000 });
  await anyadir.click();
  const tipos = main.locator('#frm_add select#id_tipo_doc');
  await expect(tipos).toBeVisible({ timeout: 20_000 });
  const libre = main.locator('#frm_add #libres input[name="sel[]"]').first();
  const valores = await opciones(tipos);
  let hayLibre = false;
  for (const v of valores.slice(0, 15)) {
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('equipajes_docs_libres'), { timeout: 20_000 }),
      tipos.selectOption(v),
    ]);
    await page.waitForLoadState('networkidle');
    if ((await libre.count()) > 0) {
      hayLibre = true;
      break;
    }
  }
  expect(hayLibre, 'algún tipo de documento tiene ejemplares libres').toBe(true);
  await libre.check();
  const puesto = await esperarSrc(page, 'equipajes_add_doc', () => main.locator('#frm_add input[onclick*="fnjs_add_doc"]').click());
  expect(puesto.success, puesto.mensaje).toBe(true);
  expect(Number(enValija()), 'el documento está en la valija').toBe(alCrear + 1);

  // --- Quitarlo
  const quitar = main.locator('#form_docs_grupo_1 input[onclick*="fnjs_modificar_form_del"]');
  await expect(quitar).toBeVisible({ timeout: 20_000 });
  await quitar.click();
  const aQuitar = main.locator('#frm_del input[name="sel[]"]').last();
  await expect(aQuitar).toBeAttached({ timeout: 20_000 });
  await aQuitar.click({ force: true });
  const quitado = await esperarSrc(page, 'equipajes_del_doc', () => main.locator('#frm_del [onclick*="fnjs_del_doc"]').first().click());
  expect(quitado.success, quitado.mensaje).toBe(true);
  expect(Number(enValija()), 'el documento ya no está en la valija').toBe(alCrear);

  // --- Eliminar la valija
  const sinValija = await esperarSrc(page, 'equipajes_eliminar_grupo', () => main.locator('#form_docs_grupo_1 input[onclick*="fnjs_eliminar_grupo"]').click());
  expect(sinValija.success, sinValija.mensaje).toBe(true);
  expect(sql(`SELECT count(*) FROM ${T}.i_egm_dl WHERE id_equipaje = ${idEquipaje}`), 'la valija ya no está').toBe('0');

  // --- Texto del listado para imprimir
  // (La pantalla anterior tiene el mismo desplegable: esperar al campo que distingue a esta.)
  await abrirPantalla(page, 'frontend/inventario/controller/equipajes_ver.php?filtro=tot&imprimir=1', 'input#imprimir[value="1"]', true);
  await main.locator('select#id_equipaje').selectOption(idEquipaje);
  const cabecera = main.locator('#exportar #cabecera');
  await expect(cabecera).toBeAttached({ timeout: 20_000 });
  await sinErrorDeBackend(page);
  await page.evaluate(() => {
    // @ts-expect-error función de la vista equipajes_ver
    fnjs_mod_texto_equipaje('cabecera');
  });
  const texto = main.locator('#frm_listado textarea[name="texto"]');
  await expect(texto).toBeVisible({ timeout: 20_000 });
  await texto.fill('Cabecera e2e');
  const guardado = await esperarSrc(page, 'equipajes_texto_listado_guardar', () => main.locator('#frm_listado input[onclick*="fnjs_guardar_listado"]').click());
  expect(guardado.success, guardado.mensaje).toBe(true);
  expect(sql(`SELECT cabecera FROM ${T}.i_equipajes_dl WHERE id_equipaje = ${idEquipaje}`)).toBe('Cabecera e2e');

  // --- Eliminar el equipaje
  await abrirPantalla(page, 'frontend/inventario/controller/equipajes_ver.php?filtro=tot&eliminar=1', 'input[onclick*="fnjs_eliminar_equipaje"]');
  await main.locator('select#id_equipaje').selectOption(idEquipaje);
  const borrado = await esperarSrc(page, 'equipajes_eliminar', () => main.locator('input[onclick*="fnjs_eliminar_equipaje"]').click());
  expect(borrado.success, borrado.mensaje).toBe(true);
  expect(dialogos.length, 'pide confirmación antes de borrar').toBeGreaterThan(0);
  expect(sql(`SELECT count(*) FROM ${T}.i_equipajes_dl WHERE id_equipaje = ${idEquipaje}`), 'ya no está en la BD').toBe('0');

  expect(inc.erroresJs, inc.erroresJs.join('\n')).toEqual([]);
  expect(inc.respuestasNoJson, inc.respuestasNoJson.join('\n')).toEqual([]);
});
