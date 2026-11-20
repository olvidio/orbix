# Procedimientos: qué pasos seguir para hacer cada cosa

[`AGENTS.md`](../../AGENTS.md) dice **cómo tiene que quedar** el código (capas, contratos, convenciones). Este documento dice **en qué orden se hace** cada tipo de trabajo y con qué órdenes se comprueba. No repite las reglas: enlaza a donde están. Si un paso de aquí contradice a `AGENTS.md`, manda `AGENTS.md` y hay que corregir este documento.

| Quiero… | Apartado |
|---|---|
| Empezar cualquier cambio | [0](#0-antes-de-tocar-nada) |
| Corregir un error | [1](#1-corregir-un-error) |
| Añadir una pantalla que solo lee (listado, ficha) | [2](#2-añadir-una-pantalla-de-lectura) |
| Añadir una acción que escribe (alta, modificación, borrado) | [3](#3-añadir-una-acción-que-escribe) |
| Añadir una entidad con su repositorio | [4](#4-añadir-una-entidad-con-su-repositorio) |
| Añadir un campo a una tabla que ya existe | [5](#5-añadir-un-campo-a-una-tabla-que-ya-existe) |
| Cambiar la base de datos | [6](#6-cambiar-la-base-de-datos-migración) |
| Cambiar lo que recibe o devuelve un endpoint | [7](#7-cambiar-el-contrato-de-un-endpoint) |
| Añadir o cambiar textos de interfaz | [8](#8-añadir-o-cambiar-textos-de-interfaz) |
| Que una pantalla salga en el menú | [9](#9-añadir-una-entrada-de-menú) |
| Probar un flujo en el navegador (E2E) | [10](#10-escribir-un-spec-e2e) |
| Mover o renombrar código sin cambiar lo que hace | [11](#11-refactor-sin-cambio-de-comportamiento) |
| Un test destapa un error que no toca arreglar ahora | [12](#12-un-test-destapa-un-error-que-no-toca-ahora) |
| Añadir un script de línea de órdenes | [13](#13-añadir-un-script-cli) |
| Dar el trabajo por terminado | [14](#14-cierre-antes-del-commit) |
| Algo falla y no sé por dónde empezar | [15](#15-síntomas-y-por-dónde-empezar) |
| Que la ayuda sepa contestar «¿cómo hago…?» | [16](#16-documentación-de-usuario-que-la-ayuda-sepa-contestar) |

## Órdenes que se usan en todos los procedimientos

Todas desde la raíz del repo, en el host. El PHP y la BD corren en Docker (`orbix-php-fpm-1`, `orbix-db-1`).

```bash
# PHPUnit, siempre con el envoltorio (va contra las BD de pruebas; mismos argumentos que PHPUnit)
php tools/qa/phpunit_protegido.php tests/unit/<modulo>
php tools/qa/phpunit_protegido.php tests/integration/<modulo>
php tools/qa/phpunit_protegido.php --filter test_nombre tests/unit/<modulo>/application/FooTest.php
php tools/qa/phpunit_protegido.php                      # suite completa

# Specs E2E de escritura, con el mismo envoltorio
php tools/qa/phpunit_protegido.php --comando "npx playwright test --config=e2e/playwright.config.ts e2e/specs/authenticated/<spec>.spec.ts"

# Sintaxis y análisis estático (objetivo: 0 errores)
php -l <fichero>
composer phpstan:file -- src/<modulo>/ frontend/<modulo>/
# si el PHP del host no sirve, dentro del contenedor:
docker exec -w /var/www/orbix orbix-php-fpm-1 php -d xdebug.mode=off libs/vendor/bin/phpstan analyse --memory-limit=2G -c phpstan-nobaseline.neon src/<modulo>/

# Huecos: entidades, VO, repos, casos de uso y controladores sin test; `$aWhere`; hashes de formulario
composer qa:huecos
```

- **No lanzar PHPUnit con `docker exec … phpunit` a mano ni con `composer test` / `tools/phpunit-docker`:** van contra las BD de desarrollo. Por qué y cómo funciona el envoltorio: [`bd_pruebas.md`](bd_pruebas.md).
- El envoltorio termina con código **2** si hay secuencias atrasadas (no ejecuta nada) y con código **1** si una tabla ha perdido filas, aunque los tests pasen. `ORBIX_HUELLA=0` ahorra unos 50 s mientras se itera sobre un test.
- El envoltorio ya pone la memoria necesaria para la suite completa y no se para en el primer fallo (usa `build/phpunit-no-stop.xml`, no el `stopOnFailure` de `phpunit.xml`).
- No lanzar dos pasadas a la vez sobre los mismos clones.
- Playwright modifica `test-results/` (versionado). Al acabar: `git checkout -- test-results/ && git clean -fdq test-results/`.

---

## 0. Antes de tocar nada

1. **Concretar el caso:** qué se hace, con qué datos, con qué usuario o rol, qué pasa ahora y qué debería pasar. Si la petición admite dos lecturas, preguntar antes.
2. Leer el baseline del módulo (`docs/dev/<modulo>_migracion_baseline.md`) y, si se van a escribir tests, [`tests/AGENTS.md`](../../tests/AGENTS.md).
3. `git status`: saber qué cambios hay ya en el árbol y no mezclarlos con el trabajo nuevo.
4. Ejecutar los tests del módulo (**verde de partida**). Si algo ya falla, apuntarlo y no mezclarlo con el cambio.
5. Localizar a quién afecta: `rg "<endpoint, clase o campo>" src frontend scripts tests e2e`.

## 1. Corregir un error

Regla completa: `AGENTS.md`, «Primero el test que falla, después el código».

1. Reproducir el caso (paso 0.1). Si solo se ve en la interfaz, reproducirlo en la instancia de pruebas (`http://orbix.local:8003/orbix`), no en desarrollo.
2. Buscar un test que ya cubra el caso y ejecutarlo. Si existe y falla, sirve.
3. Si no existe o pasa, escribir uno en el nivel más bajo que lo reproduzca: unitario → integración de repositorio → E2E.
   - Usar los **valores y tipos de producción** (VO reales, constantes del VO, ids y no etiquetas): con un mock que acepta cualquier cosa el test pasa y el error sigue.
   - Si el error está en una consulta, un caso contra el repositorio real (un mock no ejecuta SQL).
4. Ejecutarlo y **verlo fallar por el motivo esperado** (la aserción del caso, no el montaje).
5. Corregir el código, lo mínimo.
6. Ejecutar el test (pasa) y los demás tests del módulo.
7. Buscar el mismo error en otros sitios: `rg` del patrón. Si aparece, o se corrige con su test o se apunta ([12](#12-un-test-destapa-un-error-que-no-toca-ahora)).
8. [Cierre](#14-cierre-antes-del-commit). Commit `fix(<modulo>): …` con el test dentro. En el resumen, decir qué test falló antes y que pasa después.

Si no se consigue un test que falle, no se toca el código: el caso no está bien entendido o el error está en otro sitio.

## 2. Añadir una pantalla de lectura

De dentro afuera. Cada pieza con su test antes de pasar a la siguiente.

1. **Caso de uso** `src/<modulo>/application/<Algo>Data.php`: recibe un array de entrada, devuelve un array serializable. Sin `$_POST`, sin HTML, sin `HashF`. Enlaces a otras pantallas como `link_spec`; desplegables como pares con `OpcionesDesplegable`. Una situación prevista que impide mostrar los datos se devuelve como `aviso_txt`, no como error.
2. **Test unitario** del caso de uso en `tests/unit/<modulo>/application/`: al menos un caso con una fila real por cada repositorio que recorre, y la forma exacta de la salida.
3. **Controlador HTTP** `src/<modulo>/infrastructure/ui/http/controllers/<algo>_data.php`: lee con `FilterPostGet::post()`, llama al caso de uso, responde con `ContestarJson::enviar`. Sin `exit`, `echo` ni `header()`. Con el POST vacío tiene que responder JSON (lo comprueba `ControladoresLecturaSinParametrosTest`).
4. **Ruta** en `src/<modulo>/config/routes.php` y, si hay clases nuevas que inyectar, `config/dependencies.php`.
5. **Documentación IA:** añadir el endpoint a `docs/ai/<modulo>/api_resumen.md` y subir `endpoints:` en la cabecera.
6. **Controlador frontend** `frontend/<modulo>/controller/<algo>.php`: `FrontBootstrap::boot()`, `$oPosicion->nav()->enter(url, bloque, identity, state)` si la pantalla entra en la pila de navegación (`AGENTS.md`, «Manejo de Navegación y Estado»), `PostRequest::getDataFromUrl('/src/<modulo>/<algo>_data', $campos)`, firma de los `link_spec` con `HashFSignedLink`, y `ViewNewPhtml::renderizar`.
7. **Vista** `frontend/<modulo>/view/<algo>.phtml`: solo presentación. Textos con `_("…")`. En `<script>`, los valores de PHP con `json_encode(..., JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)`, no con `htmlspecialchars`.
8. **Si la pantalla tiene formulario o AJAX firmado:** un `HashF` por URL. Los campos que no viajan siempre van en `setCamposNo()` / `setCamposChk()`. Comprobar con `composer qa:hash-campos`.
9. [Textos](#8-añadir-o-cambiar-textos-de-interfaz), [menú](#9-añadir-una-entrada-de-menú) y, si es un flujo de usuario, [E2E](#10-escribir-un-spec-e2e).
10. Abrirla en la instancia de pruebas con un usuario que tenga permiso y con uno que no.
11. [Cierre](#14-cierre-antes-del-commit).

Referencias de las reglas: `AGENTS.md`, «Arquitectura `frontend/` + `src/`», «Endpoints JSON para `PostRequest`», «Despacho en proceso», «Enlaces firmados hacia la UI».

## 3. Añadir una acción que escribe

Una acción no se ejecuta al abrir una pantalla: la autoriza una cápsula `HashB` que firmó la pantalla anterior.

1. **Decidir la acción y su nombre:** un endpoint por acción (`<recurso>_update`, `<recurso>_delete`…), sin parámetro que elija qué hacer.
2. **Caso de uso** en `application/` (`<Recurso>Guardar`, `<Recurso>Eliminar`…): recibe datos ya tipados, devuelve `''` o el texto del error. Tipos tolerantes o cast en el controlador: lo que llega del POST es `string` o `null`.
3. **Tests del caso de uso:** camino feliz, cada validación, el recurso que no existe (`findById` → `null`), y cada rama por rol o por app instalada si las hay.
4. **Firmar la cápsula en la lectura previa:** el `*Data` que pinta el formulario o la lista añade `'ctx_update' => HashB::sign('<accion>', [ids…])` (por fila si es una lista). Test: abrir la cápsula con `HashB::open($token, '<accion>')` y afirmar el contenido exacto.
5. **Controlador HTTP de la acción:** lo primero, `HashB::open(FilterPostGet::post('ctx_…'), '<accion>')`; si falla, `ContestarJson::enviar(_("Operación no autorizada"), 'none'); return;`. Los ids salen de la cápsula, no del POST. Siempre responde `{success, mensaje}`. Lo comprueba `ControladoresConTokenSinTokenTest`. Modelo: `src/ubiscamas/infrastructure/ui/http/controllers/cama_delete.php`.
6. **Ruta**, `dependencies.php` y `docs/ai/<modulo>/api_resumen.md`, como en [2](#2-añadir-una-pantalla-de-lectura).
7. **Vista:** la cápsula va en un `<input type="hidden" name="ctx_…">` (o en el dato de la fila); sin ocultos con ids. Guardado con `$.ajax` + `serialize()` + `dataType: 'json'`; si `success !== true`, mostrar `mensaje`. Un borrado pide confirmación antes.
8. **Si pasa por un controlador frontend** (proxy): este solo reenvía la cápsula que le llega; nunca la pide él a un `*_data` en la misma petición (`PantallasNoFirmanSuPropiaAccionTest`).
9. **Si sube un fichero:** `MultipartUploadGuard` en el controlador y `.fail` en el `$.ajax`. Si descarga un binario por GET: `SignedDownloadToken`.
10. **Test del controlador** (opcional pero recomendable si tiene ramas): ejecutarlo en proceso como en `tests/integration/usuarios/infrastructure/controllers/UsuariosControllersRespuestaTest.php`, solo por caminos que no escriben o dentro de `DetectaEscriturasEnBd::escriturasDe()`.
11. **E2E del flujo** alta → modificar → borrar ([10](#10-escribir-un-spec-e2e)) y añadirlo a la tabla de `e2e/README.md`.
12. Probar a mano en la instancia de pruebas: éxito, sin cápsula, con la cápsula de otra acción.
13. [Cierre](#14-cierre-antes-del-commit).

Detalle del modelo: [`hash_arquitectura.md`](hash_arquitectura.md) (§4 y §10).

## 4. Añadir una entidad con su repositorio

1. **Tabla:** [migración](#6-cambiar-la-base-de-datos-migración) y aplicarla en desarrollo.
2. **Value objects** en `domain/value_objects/` con su test unitario cada uno.
3. **Entidad** en `domain/entity/` con su test (`test_set_and_get_<campo>` por propiedad).
4. **Enum de campos** `domain/criteria/<Entidad>Field.php` (`implements CriteriaField`).
5. **Contrato** `domain/contracts/<Entidad>RepositoryInterface.php`: sin `PDO`, sin nombres de tabla.
6. **Repositorio** `infrastructure/persistence/postgresql/Pg<Entidad>Repository.php`: `matching()` con `RunsCriteriaQueries`; fechas con `ConverterDate` y json con `ConverterJson`, en lectura **y** escritura, con el mismo `$type`. Si toma ids con `nextval`, la secuencia es la de **su** tabla (`SecuenciasDeRepositoriosTest`).
7. **`config/dependencies.php`:** interfaz → `Pg*`.
8. **Factory** `tests/factories/<modulo>/<Entidad>Factory.php` (`createSimple(?$id)`; id de `getNewId()`, no escrito a mano).
9. **Test de integración** `tests/integration/<modulo>/infrastructure/persistence/postgresql/Pg<Entidad>RepositoryTest.php`: guardar, actualizar, leer, no encontrado, eliminar y `matching()`. Con clave compuesta o varias filas por padre, comprobar además que cambiar o borrar una fila **no toca las demás**. Limpiar en `finally`.
10. Si la tabla es de dl y lleva `id_schema` (o un valor por defecto con la dl o la región): apuntarla en `RenombrarEsquemaDefaultsCatalog` y en el DDL de creación de esquemas del módulo (`src/<modulo>/db/`), para que los esquemas nuevos nazcan con ella.
11. `composer qa:huecos` (modo estricto para entidades, VO y repos) y [cierre](#14-cierre-antes-del-commit).

Para el esqueleto existe el generador `php tools/codegen/generar_clases.php --db=… --tabla=esquema.tabla --clase=…` (línea de órdenes; ya no es una pantalla); lo que genera hay que revisarlo contra estas reglas.

## 5. Añadir un campo a una tabla que ya existe

El campo atraviesa todas las capas; el orden evita dejar una a medias.

1. [Migración](#6-cambiar-la-base-de-datos-migración) (con su par `__sf` si toca) y aplicarla en desarrollo.
2. Value object (si el campo lo merece) + test.
3. Entidad: propiedad, getter y setter + test. Si la entidad tiene `getDatosCampos()`, el `DatosCampo` nuevo con getter y setter que existan (`DatosCamposMetodosTest`).
4. `<Entidad>Field`: el caso nuevo, si se va a buscar u ordenar por él.
5. Repositorio: lectura (todos los caminos que hidratan) y escritura; converter si es fecha o json. Test de integración que guarde y lea el valor.
6. Factory: valor por defecto si es obligatorio.
7. Caso de uso de lectura (`*Data`) y de escritura + sus tests.
8. Vista y controlador frontend. **El campo nuevo del formulario entra en el hash:** `setCamposForm()` si viaja siempre, `setCamposNo()` / `setCamposChk()` si no. `composer qa:hash-campos`.
9. `rg "<nombre de la tabla o de la entidad>"` para encontrar listados, exportaciones, copias entre bases ([`copias_entre_bases.md`](copias_entre_bases.md)) y avisos de cambios que también deban llevarlo.
10. [Textos](#8-añadir-o-cambiar-textos-de-interfaz), tests del módulo, E2E del flujo si existe, y [cierre](#14-cierre-antes-del-commit).

## 6. Cambiar la base de datos (migración)

Guía completa: [`db/migrations/README.md`](../../db/migrations/README.md).

1. Fichero `db/migrations/YYYYMMDDHHMM_descripcion__<db>.sql` con `<db>` = `comun`, `sv-e`, `sv` o `sf`.
2. **Cada `__sv` / `__sv-e` lleva su equivalente `__sf`** (esquemas con sufijo `f`, `publicv` → `publicf`).
3. Tablas de dl: comodín `*.` para todos los esquemas. Comprobar que la tabla existe en el esquema antes de tocarla (no todas las dl tienen todos los módulos).
4. **Idempotente:** `IF NOT EXISTS`, comprobar columnas antes de `RENAME` / `UPDATE`; `migracion_detener_si(...)` si ya está aplicada. El runner la reaplica si el fichero cambia.
5. No mezclar estructura y datos en un fichero si se puede evitar (una mezcla se trata como estructura).
6. Aplicarla en **desarrollo** desde el menú de `devel_db_admin` (`migraciones_lista`), en sesión sv y en sesión sf.
7. Los clones de pruebas se regeneran solos en la siguiente pasada de tests al ver que el esquema de desarrollo cambió (`php tools/qa/bd_pruebas.php estado` para verlo; `regenerar` si solo cambiaron datos). No puede haber conexiones abiertas a desarrollo en ese momento.
8. Si la migración borra o mueve filas que podrían hacer falta, exportarlas antes a `log/db/` con las directivas `@orbix_export_csv`.
9. Si cambia una tabla que tiene copia en `comun` (`cp_*`, `cd_*`, `cu_*`): revisar la copia.
10. Código que usa el cambio: [4](#4-añadir-una-entidad-con-su-repositorio) o [5](#5-añadir-un-campo-a-una-tabla-que-ya-existe). Migración y código, en el mismo commit o en commits seguidos.

Ojo: en Docker y en los clones todas las tablas son legibles por todos; en producción las madres de `global` no lo son para el usuario de una dl. Un `SELECT` nuevo sobre una tabla madre puede pasar los tests y fallar en producción: mirar con qué conexión lee el repositorio.

## 7. Cambiar el contrato de un endpoint

1. `rg "<ruta o nombre del endpoint>" frontend src scripts e2e tests` y hacer la lista de consumidores: PHP (`PostRequest`), JS (`$.ajax`), plantillas duplicadas en otros módulos, apps externas (`app-movil-contrato.spec.ts`).
2. Test que falle con el contrato nuevo (o, si es un refactor, tests que fijen el actual).
3. Cambiar el backend y **todos** los consumidores en el mismo cambio.
4. Si cambian los campos que envía un formulario o un AJAX firmado: `HashF` nuevo para esa URL y `composer qa:hash-campos`.
5. Si se parte un endpoint multiuso: una ruta y un caso de uso por rama, migrar los consumidores y borrar el antiguo cuando `rg` no encuentre referencias.
6. Actualizar `docs/ai/<modulo>/api_resumen.md`.
7. `php -l`, tests del módulo y una prueba en el navegador por consumidor.

Lista completa: `AGENTS.md`, «Checklist al cambiar el contrato de un endpoint en `src/`».

## 8. Añadir o cambiar textos de interfaz

1. En el código: `_("…")` con comillas dobles y literal (Twig: `{{ "…"|trans }}`).
2. `php tools/audit/audit_gettext_comillas.php` sobre lo tocado.
3. `php tools/i18n/actualizar.php` (menús de la BD → `.pot` → `msgmerge` → traducción → `.mo` → informe). Sin BD ni clave de Groq: `--sin-menus --sin-traducir` y traducir a mano.
4. **Leer el informe de cadenas nuevas** y corregir en los `.po` lo que el modelo no supo (vocabulario de la casa: `tools/i18n/glosario.json`). Recompilar el `.mo` si se corrige.
5. `php tools/audit/audit_po_cobertura.php`: ninguna cadena nueva vacía ni fuzzy.
6. Commit con el código, el `.pot`, los cuatro `.po` y los cuatro `.mo`.

Detalle: [`traducciones_gettext.md`](traducciones_gettext.md).

## 9. Añadir una entrada de menú

El menú son datos de la BD, no código ([`supervision_modulos_menus_procesos.md`](supervision_modulos_menus_procesos.md), §3).

1. Crear o reutilizar el **metamenú** (URL del controlador frontend + parámetros).
2. Colgar el ítem en el **gestor de menús** del layout: texto, padre, grupo de menús, bits de oficina.
3. Comprobar que los roles que deben verla tienen ese grupo de menús, y que un rol restringido (casa, centro, sacd) llega a la pantalla solo si le corresponde.
4. Para que llegue a las demás instalaciones: migración de datos (`aux_*` en `__sv-e` y `__sf`, `ref_*` en `__comun`).
5. El nombre del menú se traduce: [8](#8-añadir-o-cambiar-textos-de-interfaz) (`actualizar.php` lo recoge de la BD).
6. `formularios-del-menu.spec.ts` abre todas las entradas de menú: ejecutarlo para la nueva.
7. Si la guía de usuario la menciona, regenerar `docs/guias/_referencia_menus.md` (se confirma dentro del submódulo `docs/guias`).

## 10. Escribir un spec E2E

Guía y lista de ayudas: [`e2e/README.md`](../../e2e/README.md), «Flujos de escritura».

1. Mirar qué hace el controlador antes de abrirlo: hay pantallas que son una acción.
2. Spec en `e2e/specs/authenticated/<modulo>-<flujo>.spec.ts`. Empezar con `abrirApp` + `abrirPantalla`.
3. Datos propios marcados con `marcaE2e()`; nunca fichas reales. Limpieza con `limpiar` / `sqlDev` al final, aunque el test falle.
4. Selectores por `name` / `id` / `onclick`, no por texto. `marcarFila()`, `elegirOpcion()`, `esperarSrc()`, `redEnCalma()` en lugar de esperas a ojo.
5. Lanzarlo con el envoltorio (`--comando`). Ver que **falla** si se rompe lo que prueba.
6. Estabilidad: `--repeat-each=8`.
7. Comprobar que no quedan filas con la marca y que los datos que ya había no han cambiado.
8. Añadir la fila a la tabla de `e2e/README.md` y restaurar `test-results/`.

Límite conocido: el usuario de E2E (esquema `H-dlb`) no tiene los módulos que su dl no tiene instalados (p. ej. `ubiscamas`); esos se prueban con tests unitarios o a mano.

## 11. Refactor sin cambio de comportamiento

1. Buscar los tests que cubren el código. Si no hay, escribir primero uno que fije el comportamiento actual.
2. Ejecutarlos: pasan.
3. Refactor. Sin arreglos «de paso»: un error que aparezca se trata como [1](#1-corregir-un-error) o [12](#12-un-test-destapa-un-error-que-no-toca-ahora), en otro commit.
4. Ejecutarlos: pasan sin haberlos cambiado (salvo nombres o rutas movidos).
5. `composer phpstan:file -- <rutas>`, `composer qa:huecos` y los tests-guardia (`tests/unit/shared`, `tests/integration/shared`).
6. Commit `refactor(<modulo>): …`.

## 12. Un test destapa un error que no toca ahora

1. No relajar el test ni ocultar el error.
2. Fijar el comportamiento tal como es: nombre que lo describa y docblock que empiece por `Hallazgo:` y diga qué debería pasar (`rg "Hallazgo:" tests/` los lista).
3. Si la decisión es del usuario (regla de negocio, datos), apuntarla en «Dudas pendientes» del plan en curso o en [`backlog.md`](backlog.md), con la opción más conservadora aplicada.
4. Cuando se corrija: commit `fix(<modulo>)` aparte, que cambia ese test; comprobar que el test nuevo falla sin el arreglo (`git stash -- <fichero>`).

Lo mismo con un **test-guardia** que falla por un caso legítimo: se añade el caso a su lista de excepciones **con el motivo**; no se cambia la regla.

## 13. Añadir un script CLI

1. Carpeta: `tools/<audit|fix|i18n|phpstan|qa>/`, nunca `scripts/`. Generadores de documentación: `docs/scripts/`.
2. Docblock con la orden completa (`php tools/<sub>/<nombre>.php`), raíz con `dirname(__DIR__, 2)`, secretos por variable de entorno.
3. Si modifica ficheros o datos: `--dry-run` por defecto y `--apply` para escribir.
4. Si escribe informes: `docs/dev/reports/`.
5. Si es una comprobación que debe pasar siempre: añadirla a `composer.json` (y a `qa:huecos` si procede) y a `tools/README.md`.

## 14. Cierre antes del commit

1. `php -l` de cada fichero tocado.
2. Tests del módulo tocado (unit e integración) con el envoltorio; si se tocó `shared` o algo transversal, la suite completa.
3. `composer phpstan:file -- <rutas tocadas>`: 0 errores.
4. `composer qa:huecos`. Si se cubrió un hueco de la línea base: `php tools/qa/check_test_application.php --update-baseline`.
5. Textos nuevos: [8](#8-añadir-o-cambiar-textos-de-interfaz) completo.
6. Rutas nuevas: `docs/ai/<modulo>/api_resumen.md`. Si cambió lo que el usuario ve o hace (un botón, un paso, un mensaje, una regla): [16](#16-documentación-de-usuario-que-la-ayuda-sepa-contestar).
7. Lo que se decidió no hacer: a [`backlog.md`](backlog.md) (qué, por qué no ahora, notas).
8. `git status`: sin restos (`test-results/`, ficheros temporales, copias de `phpunit.xml`), sin ficheros vacíos, sin `.env`.
9. Repasar el «Checklist obligatorio en cada PR» de `AGENTS.md`.
10. Commit: `tipo(<modulo>): frase en español que dice qué cambia para quien lo usa`, con `tipo` = `fix`, `feat`, `refactor`, `test` o `docs`. Un arreglo y su test van juntos; un arreglo y un refactor, separados. En el cuerpo, la causa y lo que no se ha tocado.

## 15. Síntomas y por dónde empezar

| Síntoma | Causa habitual | Qué mirar |
|---|---|---|
| Al enviar un formulario o un AJAX, vuelve al inicio (302) sin mensaje | Los campos enviados no son los firmados por `HashF` | `composer qa:hash-campos`; `e2e/form-hash-audit.ts` (`diagnosticar`); campo que no viaja siempre → `setCamposNo()` / `setCamposChk()`; `hpos` en `PostRequest` |
| «Operación no autorizada» | Falta la cápsula `HashB`, es de otra acción o la sesión cambió | Que el `*_data` firme con el mismo nombre de acción que abre el controlador; que la vista envíe `ctx_…` |
| «Respuesta no JSON» o HTML de un error fatal con estado 200 | `findById()` devolvió `null` y se llamó a un método; `echo` / `exit` en un controlador de `src/` | El log de PHP; test con `findById` → `null`; `ControladoresSinExitTest` |
| «Página con seguridad desactivada» | Una URL firmada salió con `&amp;h=` dentro de un `<script>` | `htmlspecialchars` en `<script>` → `json_encode(...)`; `VistasSinHtmlspecialcharsEnScriptTest` |
| El controlador de `src/` no ve los parámetros | Lee con `filter_input(INPUT_POST, …)` y la llamada es en proceso | `FilterPostGet::post()` |
| `invalid input syntax for type integer` en una búsqueda | Se pasan etiquetas (o un texto con formato) y no ids a un `Filter` | `array_keys(...)` de los `getArrayXxx()`; caso contra el repositorio real |
| `Object of class TimeLocal could not be converted to string` | Cast de una fecha u hora | `?->format('H:i')` |
| Un desplegable sale en otro orden que el del backend | `opciones` va como mapa y JS reordena las claves numéricas | `OpcionesDesplegable::enOrden()` + `fnjs_construir_desplegable` |
| Una fecha cambia día y mes según el usuario | `DateTimeLocal::createFromLocal()` con un texto de formato fijo | Parsear el formato conocido; probar con la sesión en `en_US` |
| Una traducción nueva no aparece | La entrada está fuzzy o el `.mo` no se compiló | `php tools/audit/audit_po_cobertura.php`; `msgfmt --check` |
| Tras un test, los siguientes fallan con `relation "…" does not exist` | Un test dejó `$_SESSION` (esquema) o un repositorio del contenedor cambiado | Guardar y restaurar `$_SESSION` y `$GLOBALS['container']`; `clone` antes de `setoDbl()` |
| `relation "…" does not exist` en una sola pantalla o test | El módulo no está instalado en la dl de la sesión | Otro esquema (`UbiscamasEnDlInstalada`) o probar de otra manera |
| Un test falla a veces («Ya existe…») | Datos aleatorios de factory sobre datos reales, o conexión compartida cambiada | Datos fijos con marca propia; limpiar antes y después |
| Un spec E2E falla a veces | Clic o selección antes de que la pantalla se repinte | `marcarFila()`, `elegirOpcion()`, `redEnCalma()`; `--repeat-each=8` |
| El envoltorio termina con código 2 sin ejecutar nada | Secuencias por detrás de los ids | `comun` / `sv-e`: se ajustan al máximo de su tabla; `sv`: avisar, hay que mirar producción |
| El envoltorio termina con código 1 con los tests en verde | Una tabla perdió filas: un test borró algo que no creó | La tabla que lista; el test que usa ids fijos o una secuencia ajena |
| Pasa en los tests y falla en producción con «permiso denegado» | La tabla madre no es legible para el usuario de la dl | La conexión del repositorio (`resto`, `public`); ver [`backlog.md`](backlog.md), «Fusionar `d_asistentes_out`» |

## 16. Documentación de usuario: que la ayuda sepa contestar

La pantalla **Preguntar a la documentación** (`src\shared\application\AyudaPreguntar`) busca, sin IA ni red, en tres sitios:

| Fuente | Qué es | Para qué pregunta sirve |
|---|---|---|
| `docs/guias/<area>/*.md` | Guías **por tarea**, escritas y revisadas a mano (submódulo `orbix_docs`) | «¿Cómo hago X?»: cada apartado es una respuesta y la ayuda enseña sus pasos completos |
| `docs/ai/<modulo>/` | Fichas por pantalla y por flujo, generadas del catálogo | «¿Qué hace esta pantalla?» |
| `docs/manual/<modulo>.md` | Manual por módulo, generado | Consulta general; ayuda contextual de cada pantalla |

Una pregunta de usuario se contesta con una **guía**. Las fichas generadas describen pantallas, no tareas, y una tarea suele cruzar varios módulos.

**Cuándo toca:** al añadir una pantalla o una acción, y siempre que un arreglo cambie lo que el usuario ve o hace: un botón, un paso, una confirmación nueva, un mensaje, quién puede hacerlo, el caso «de otra dl» o «de paso».

1. **Escribir la pregunta** como la diría alguien de la oficina («¿cómo apunto a una persona de otra dl?») y añadirla a `tools/audit/ayuda_preguntas.tsv` con la guía que debe contestarla.
2. **Verla fallar:** `php tools/audit/ayuda_preguntas.php` la lista como `NO` o `CASI`; `php tools/audit/ayuda_preguntas.php "la pregunta"` enseña qué responde hoy la ayuda.
3. **Buscar la guía** en `docs/guias/00_indice.md`. Si la tarea no tiene guía, crearla con `docs/guias/_plantilla_guia.md`.
4. **Escribir o corregir el apartado** contra el código, no de memoria: los nombres de botones y mensajes salen de los `_("…")` de la vista y del caso de uso; el orden de los pasos, del spec E2E del flujo si existe (`e2e/specs/authenticated/`). Lo que no se pueda confirmar se marca «(pendiente de confirmar)» y se apunta en «Preguntas abiertas» de `docs/guias/PLAN_GUIAS_PROCESOS.md`.
5. **Un caso particular, un subapartado:** «si es de otra delegación», «si es de paso», «si ya está firmada». Quien llega desde la ayuda solo lee ese apartado.
6. **Declarar las preguntas** bajo el título del apartado: `<!-- preguntas: "¿…?" | "¿…?" -->`. Convención completa: `docs/guias/CONVENCIONES.md`.
7. **Mensajes de error nuevos:** a la tabla «Errores frecuentes» de la guía, con el texto exacto.
8. **Verla pasar:** `composer qa:ayuda` (todas `OK`).
9. Si cambió una pantalla: regenerar el manual y las fichas del módulo (`php docs/scripts/generar_manual_usuario_modulo.php <modulo> --force`; no usar `generar_documentacion_modulo.sh --force`, que pisa el catálogo revisado) y `php docs/scripts/generar_indice_ayuda.php`.
10. **Confirmar en dos sitios:** las guías son un submódulo. Primero commit dentro de `docs/guias` (repo `orbix_docs`), después `git add docs/guias` en Orbix junto con la batería.

En el servidor, la ayuda solo encuentra las guías si el submódulo está descargado (`git submodule update --init`); sin él sigue funcionando con el manual y las fichas.

