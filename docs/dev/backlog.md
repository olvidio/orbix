# Backlog técnico (diferido)

Listado corto de **mejoras o migraciones decididas pero no ejecutadas**. No sustituye a issues/Trello del equipo si los usáis; sirve como memoria dentro del repo.

Formato sugerido por ítem:

- **Qué**, **por qué no ahora**, **notas / enlaces** (ficheros, hilos).

---

## Pendientes

### Notas ancladas al acta (dejar de mover notas en traslados Orbix)

- **Qué:** Modelo B de [`notas_modelo_acta.md`](notas_modelo_acta.md): notas fijas en la DL del acta; expediente agregado; certificado automático solo hacia entidad externa; deprecar `e_notas_otra_region_stgr` / placeholders internos.
- **Progreso (2026-07):** Slices 1–3 y 5 hechos (`EditarPersonaNota`, `Trasladar::copiarNotas` no-op, `ExpedienteNotasPersona`, certificados desacoplados). Herramientas audit/fix en `tools/` (Slices 4 y 6). Tests actualizados al contrato B.
- **Pendiente:** flujo certificado destino externo; informes STGR locales.
- **Notas:** Mapa SSOT + grabar nota en esquema del acta (`EditarPersonaNota`). Diag tipo 1: `tools/audit/diag_notas_otra_region_mapa.sql`.

### Comprobación de datos al borrar una persona de paso (desactivable, provisional)

- **Qué:** `PersonaEliminar` mira antes de borrar una persona de paso si tiene asistencias, cargos, encargos, notas, matrículas, docencia o zonas en alguna dl (`PgPersonaDePasoDatosRepository`). En producción el usuario de BD de cada dl no puede leer esas tablas madre y la comprobación falla siempre, así que se puede desactivar con `ORBIX_PERSONA_DE_PASO_BORRAR_SIN_COMPROBAR=1` en `.env`: con el flag se borra **sin mirar**.
- **Pendiente:** decidir cómo se leen los datos de todas las dl y quitar el flag. Para las asistencias, ver la fusión de abajo (`publicv.d_asistentes_all`). Falta decidir sobre cargos (`global.d_cargos_activ`), encargos (`global.encargos_sacd`), docencia (`global.d_docencia_stgr`), zonas (`global.zonas_sacd`), notas (`publicv.e_notas`) y matrículas (`publicv.d_matriculas_activ`): moverlas a `publicv`, darles lectura o una función `SECURITY DEFINER` que solo diga si hay datos.
- **Asistencias (2026-11-18):** la comprobación mira solo `restov.d_asistentes_ex` (`restof` en sf), con la conexión de `resto` (`oDBER_Select`; en producción el usuario de la dl no puede leer esa tabla, y `PgCargoOAsistente` la lee igual desde el 2026-11-19), donde se graban las asistencias de las personas de paso (`id_nom` negativo): alta normal y cargos (`AsistenteActividadService::getRepoAsistente`) y, desde esta fecha, también la del sacd asignado (`SacdAsignar`; `SacdEliminar` la borra de ahí si `propio = false`). `PeticionesIncorporar` no incorpora a personas de paso. El planning de una persona y los solapes de sacd (`PgCargoOAsistente`) leen también `d_asistentes_ex`. Las asistencias de sacd de paso anteriores, que estaban en `d_asistentes_dl`, las mueve a `d_asistentes_ex` la migración `202611191000_d_asistentes_de_paso_de_dl_a_ex` (`__sv-e` y `__sf`; 71 filas en desarrollo). Las filas sueltas de personas de paso en `d_asistentes_out` y en la madre `d_asistentes_de_paso` las mueve `202611191100_d_asistentes_de_paso_sueltas_a_ex` (6 en desarrollo). Las que ya tenían fila en `d_asistentes_ex` para la misma actividad (2 en desarrollo) se borran del origen (vale la de ex); antes, `202611191050_d_asistentes_de_paso_duplicadas_export` las guarda en `log/db/asistencias_de_paso_duplicadas_{sv,sf}.csv`.
- **Riesgo mientras tanto:** con el flag se puede borrar una persona de paso que tenga datos en otra dl, que quedan huérfanos.

### Fusionar `d_asistentes_out` en `d_asistentes_dl` (una sola madre legible)

- **Qué:** dejar el árbol de asistencias en `publicv.d_asistentes_all` → `"<dl>".d_asistentes_dl` (una por dl, con las asistencias dl y out juntas) + `restov.d_asistentes_ex`. Quitar los niveles intermedios `global.d_asistentes_dl` y `publicv.d_asistentes_de_paso` y las tablas `"<dl>".d_asistentes_out`. «Out» pasa a deducirse (la actividad no la organiza mi dl) o a ser solo el valor de `id_tabla`.
- **Por qué:** la separación era para que una dl no viera los asistentes de otra, y ya no importa (decisión del 2026-11-18); además no se cumple, porque la aplicación lee por `d_asistentes_all`, que es legible. No da rendimiento (≈98.000 filas dl, 12.500 out y 2.900 ex en desarrollo) y obliga a mover filas entre hijas y a buscar «en cuál está» (`AsistenteApplicationService`, `PgAsistenteOutRepository`).
- **Por qué no ahora:** hay que migrar datos (en `H-dlbv` hay 24 pares `(id_activ, id_nom)` repetidos en dl y out: resolverlos antes), y tocar `DBView` (vistas por `id_schema`), los avisos de cambios y las herramientas de esquemas de `devel_db_admin`.
- **Notas:** en producción las madres de `global` no son legibles por las dl y las de `publicv` sí; en Docker y en los clones `pruebas-*` todas tienen permisos para `PUBLIC`, así que los tests no ven esos fallos (el borrado de personas de paso falló por eso el 2026-11-18). Convendría que los clones de pruebas tuvieran los permisos de producción. `PgPersonaDePasoDatosRepository` todavía lee madres de `global` sin madre en `publicv` (`d_cargos_activ`, `encargos_sacd`, `d_docencia_stgr`, `zonas_sacd`): moverlas a `publicv` o darles lectura.

### Migración `ServerConf` → `.env` (y bootstrap unificado)

- **Qué:** Cargar configuración por instalación (rutas, host, dmz, `DIR_PWD`, etc.) vía `.env` / variables de entorno en lugar de (o como capa sobre) constantes en `ServerConf`.
- **Por qué no ahora:** Refactor grande: `ServerConf::*` aparece masivamente (`ConfigGlobal` + muchos entrypoints); hace falta prelude común muy temprano y sustituir `const`/inicializadores de propiedades por lectura en tiempo de ejecución.
- **Notas:** Análisis en conversación; cuidado con `private $dir_base = ServerConf::DIR . '...'` y con `css/*.php`, `scripts/*.js.php`, CLI y tests (`getDIR_PWD()` / modo test).

### Separar `PermisosActividades` (snapshot en sesión / consultas en backend)

- **Qué:** `src\permisos\domain\PermisosActividades` mezcla la matriz de permisos cacheada en sesión (`aPermDl`, `aPermOtras`) con consultas a BD (actividad por `id_activ`, fases completadas, `getPermisoCrear`). Objetivo: un tipo serializable solo con la matriz, y las consultas como casos de uso en `src/` servidos por endpoint.
- **Por qué no ahora:** la clase tiene ~900 líneas y la usan muchas pantallas; hoy funciona reinyectando repositorios con `DependencyResolver` al deserializarse.
- **Notas:** redacción original del objetivo en [`agents_reglas_retiradas_historico.md`](agents_reglas_retiradas_historico.md); el frontend ya pasa por `frontend/shared/session/SessionPermActividades.php` y los endpoints `actividad_fases_completadas_datos` / `actividad_fase_completada_datos`.

### Camino interno opcional para `PostRequest` sin HTTP

- **Estado (2026-11-10):** ya existe un despacho en proceso (`PostRequest::dispatchInProcess`, `InProcessSrcDispatch`) que hace `require` del controlador; ver «Despacho en proceso de `PostRequest`» en `AGENTS.md`. Lo que sigue aplazado es la variante descrita aquí (dispatcher que invoque directamente el caso de uso).
- **Qué:** Mantener HTTP como contrato base entre `frontend/` y `src/`, válido tanto si front y API están en servidores separados como si comparten instalación. En instalaciones monolito, estudiar una optimización opcional para que `PostRequest::getDataFromUrl('/src/...')` pueda resolver algunas rutas mediante un dispatcher interno que invoque directamente el mismo caso de uso de `application/`, sin abrir una subpetición HTTP.
- **Por qué no ahora:** Mientras haya controladores HTTP con lógica mezclada (`$_POST`, `echo`, `exit`, headers, validación de hash, `ContestarJson`, etc.), no es seguro saltarse HTTP de forma general. Incluir controladores PHP como atajo acoplaría la ejecución a efectos laterales de la capa HTTP.
- **Notas:** Requisito previo para cada endpoint candidato: controlador `src/.../infrastructure/ui/http/controllers/*.php` fino, que solo lea request, invoque un caso de uso/servicio de `application/` y responda con `ContestarJson::enviar`. El futuro dispatcher interno debería ser progresivo: si una ruta está registrada y preparada, llama al caso de uso; si no, fallback a HTTP. No poner esta decisión en `ContestarJson`, cuya responsabilidad debe seguir siendo solo envolver la respuesta JSON.

### Tests del DDL de esquemas: lo que queda

Con las BD de pruebas (2026-11-15) ya hay un test de las operaciones reales: `tests/integration/devel_db_admin/application/EsquemaCicloDeVidaTest.php` (crear usuarios y esquema, copiar datos, renombrar, verificar y corregir un renombre a medias, eliminar, absorber una dl en otra y mover una tabla de sv a sv-e), que solo corre con `WEBDIR=pruebas` y sobre una copia temporal de los ficheros de contraseñas.

- **Sin test real:** `zonassacd/ZonasAComun` (migración de un solo uso, ya aplicada; su simulación sí tiene test). El resto está cubierto: `MigracionesEjecutarTest`, `ModuloInstaladoTablesServiceTest` (que ejercita `AppDB` y `DBAbstract`) y `RenombrarEsquemaDefaultsCatalogTest`.
- **Ojo al escribirlos:** los roles de PostgreSQL y los ficheros `*.roles.inc` son comunes a desarrollo y pruebas (usar nombres de esquema propios y `ConfigDB::$dirPwdOverride`); `EliminarEsquemaDl` no quita las filas del esquema del registro `db_idschema` de sv y sv-e.
- **Notas:** pasos 1.7, 2.9 y 2.10 de [`revision_tests_plan.md`](revision_tests_plan.md).

### Piloto de Infection (tests de mutación) en `permisos`

- **Qué:** medir con Infection si los tests de `permisos` (97 % de líneas) detectan cambios en el código, como piloto antes de extenderlo.
- **Por qué no ahora:** Infection no está instalado; añadirlo cambia `composer.json`/`composer.lock` (duda 8 del plan).
- **Notas:** paso 2.1 de [`revision_tests_plan.md`](revision_tests_plan.md).

### Cobertura pendiente de los módulos con menos tests

- **Qué:** `inventario` (31 %, casi todo lógica en controladores), `zonassacd` (47 %), `utils_database` (48 %), `cartaspresentacion` (43 %), `misas` (60 %: faltan los constructores procedurales `cuadricula_zona_grid_data_build`, `ver_misas_zona_data_build`) y `shared` (38 %).
- **Por qué no ahora:** la fase 2 priorizó lo que escribe datos o decide permisos; aquí lo que queda es sobre todo lectura y lógica en controladores que conviene pasar antes a `application/`.
- **Notas:** cifras en [`reports/cobertura_por_modulo.md`](reports/cobertura_por_modulo.md); paso 2.10 del plan.

### E2E: más flujos y pantallas por formulario

- **Qué:** specs de flujos de escritura para los módulos con más flujos sin cubrir (inventario, misas, ubis, encargossacd, cambios, procesos) y una pasada «carga sin error» de las pantallas que no salen como enlace directo del menú (se abren por POST desde un formulario).
- **Por qué no ahora:** la fase 3 cubrió los flujos críticos que pedía el plan (personas, actividades, asistentes, notas, usuarios, subidas y un rol restringido).
- **Notas:** inventario y reglas en [`e2e/README.md`](../../e2e/README.md).

### Limpieza de las marcas de dossier en los tests PHPUnit

- **Qué:** los tests de integración que crean personas (ids aleatorios de las factories) dejan filas en `d_dossiers_abiertos` cuando las borran (unas 280 de un solo día de ejecuciones); los specs E2E ya limpian las suyas.
- **Por qué no ahora:** no afecta a los resultados; hay que localizar qué tests generan esas marcas y añadir la limpieza.
- **Notas:** hallazgo 3 de la sección 28 del plan.

### Endpoints que escriben sin cápsula `HashB`

Controladores de `src/` que escriben y no abren ninguna cápsula (fase 6.5 del plan de tests; duda 21). La lista inicial salía de un filtro por el nombre de la acción y no veía, por ejemplo, `persona_nota_editar`; al hacer cada módulo se revisan todos sus controladores.

- **Hecho (2026-11-15), `notas`:** `acta_pdf_subir`, `acta_pdf_eliminar`, `acta_ver_add_persona`, `persona_nota_nueva`, `persona_nota_editar`, `tessera_copiar`.
- **Hecho (2026-11-15), `asistentes`:** `asistente_guardar`, `asistente_eliminar`, `asistente_plaza_asignar` (`AsistenteMutationCtx`).
- **Hecho (2026-11-15), `certificados`:** `certificado_emitido_pdf_upload`, `certificado_recibido_pdf_upload` (la cápsula del formulario que guarda el certificado), `certificado_emitido_enviar` (cápsula de la fila), `certificado_emitido_guardar_pdf` (cápsula que devuelve `certificado_emitido_guardar`).
- **Hecho (2026-11-15), `procesos` y `menus`:** `actividad_proceso_generar`, `menus_exportar`, `menus_exportar_ref_a_ficheros`.
- **Hecho (2026-11-19), resincronizaciones web** (`sacd_resincronizar`, `cargos_activ_resincronizar`, `centros_resincronizar`): exigen el permiso de menú `admin_sv` (`PermisoHerramientasBdGuard`); siguen sin cápsula las de sacd y cargos, que no tienen pantalla que la firme. `menus/menus_generar_txt` se retiró el 2026-11-16.
- **No lo necesitan:** `actividades/tipo_activ_form_nuevo`, `inventario/lista_docs_asignar_ctr` y `_dlb` (pintan un formulario y firman ellos la cápsula), `usuarios/restablecer_password_guardar` y `recuperar_2fa_mail` (flujo anónimo con enlace firmado).
- `notas/acta_imprimir_presentacion_data` marca el acta como impresa al pedir los datos para imprimirla: es una lectura que escribe, a propósito; no entra en el test-guardia porque sin acta no hace nada.

### Menús: pantalla de ficheros que falta

- Las entradas de menú «exportar / importar menús a ficheros» apuntan a `frontend/menus/controller/menus_ficheros.php?accion=…`, que no existe. Importar tiene pantalla nueva (`menus_importar_de_ficheros_a_ref.php`); **exportar a ficheros no tiene ninguna**. El endpoint `menus_exportar_ref_a_ficheros` exige desde el 2026-11-15 una cápsula que nadie firma, así que no se puede ejecutar desde la web. Hacer la pantalla (con confirmación, que firme `ctx_ficheros`) y corregir las entradas de menú.

### Certificados emitidos sin prueba en el navegador

Los flujos de certificados emitidos (imprimir y guardar el PDF, subir el PDF firmado, enviar) solo existen en una región del stgr; el usuario de E2E es de una dl y no puede abrirlos. Están cubiertos por tests de contrato, no por specs. Haría falta un usuario de E2E en un esquema de región.

### Lecturas que borran según los filtros de la pantalla

`PeticionesActivData` ya no borra las peticiones de plazas que no salen en la lista pintada (duda 19 del plan de tests, resuelta), y el botón «petición ca / crt» no se ofrece a las personas de paso. `PgDelegacionRepository::Eliminar` y `PgRegionRepository::Eliminar` borran por el nombre, no por el id.

### Restos que deja la suite de PHPUnit

De forma intermitente, una fila «test_nom_activ_vo» en `public.a_actividades_all` (tabla padre). Localizar el test y limpiar. (Las 5 marcas de dossier 1303 que dejaban los tests de `tests/unit/notas` ya se limpian: `BuscaPersonaNotasTrait`, 2026-11-15.) (La dirección de casa -10019001 «test_poblacion_vo» que dejaba `PgCartaPresentacionRepositoryTest`, que no tenía `tearDown()`, también: 2026-11-16.)
