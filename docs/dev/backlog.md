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

### Proteger el endpoint web de resincronización de cargos

- **Qué:** `/src/actividadcargos/cargos_activ_resincronizar` (GET o POST) incluye el script CLI `src/actividadcargos/infrastructure/cli/cargos_activ_resincronizar.php`. Con `aplicar=1` reescribe `cd_cargos_activ_dl` en los esquemas de todas las dl, y lo puede lanzar cualquier usuario autenticado: no pide token `HashB` ni rol (solo exige estar en el interior sv y no ser DMZ). Además devuelve HTML desde `src/`. Opciones: exigir rol de administrador y token, o quitar la ruta web y dejarlo solo como CLI.
- **Por qué no ahora:** decisión del 12-11-2026 de dejarlo como está de momento.
- **Notas:** detectado en el paso 2.4 de [`revision_tests_plan.md`](revision_tests_plan.md) (sección 20, «Pendiente de decisión»).

### Proteger el endpoint web de resincronización de sacd

- **Qué:** `/src/personas/sacd_resincronizar` (GET o POST) incluye el script CLI `src/personas/infrastructure/cli/sacd_resincronizar.php` (reconciliación de `cp_sacd` en comun). Con `aplicar=1` escribe en todos los esquemas sin token `HashB` ni comprobación de rol. Mismo caso y mismas opciones que el de cargos.
- **Por qué no ahora:** se aplica por analogía la decisión del 12-11-2026 sobre el de cargos; pendiente de confirmar (plan, «Dudas pendientes para el usuario»).
- **Notas:** detectado en el paso 2.5 de [`revision_tests_plan.md`](revision_tests_plan.md) (sección 21).

### Tests del DDL de esquemas con una BD aislada

- **Qué:** las operaciones que crean, renombran, absorben o eliminan esquemas y tablas no tienen tests: `devel_db_admin` (`AbsorberEsquema`, `EliminarEsquemaDl`, `MoverTabla`, `RenombrarEsquema`, `MigracionesEjecutar`, `DBAlterSchema`…; 22 % de cobertura), `configuracion/ModuloInstaladoTablesService`, `utils_database/DBAbstract` y el script de migración `zonassacd/ZonasAComun`.
- **Por qué no ahora:** ejecutarlas contra la BD de desarrollo compartida es destructivo; hace falta una BD (o un contenedor) desechable que se cree y se tire en cada ejecución.
- **Notas:** pasos 1.7, 2.9 y 2.10 de [`revision_tests_plan.md`](revision_tests_plan.md).

### Piloto de Infection (tests de mutación) en `permisos`

- **Qué:** medir con Infection si los tests de `permisos` (97 % de líneas) detectan cambios en el código, como piloto antes de extenderlo.
- **Por qué no ahora:** Infection no está instalado; añadirlo cambia `composer.json`/`composer.lock` (duda 8 del plan).
- **Notas:** paso 2.1 de [`revision_tests_plan.md`](revision_tests_plan.md).

### Cobertura pendiente de los módulos con menos tests

- **Qué:** `inventario` (31 %, casi todo lógica en controladores), `zonassacd` (47 %), `utils_database` (48 %), `cartaspresentacion` (43 %), `misas` (60 %: faltan los constructores procedurales `cuadricula_zona_grid_data_build`, `ver_misas_zona_data_build`) y `shared` (38 %).
- **Por qué no ahora:** la fase 2 priorizó lo que escribe datos o decide permisos; aquí lo que queda es sobre todo lectura y lógica en controladores que conviene pasar antes a `application/`.
- **Notas:** cifras en [`reports/cobertura_por_modulo.md`](reports/cobertura_por_modulo.md); paso 2.10 del plan.

### Controladores de `src/` que lanzan excepciones si falta un parámetro

- **Qué:** unos 15 controladores de lectura responden con una excepción (no con un JSON de error) cuando falta un parámetro obligatorio (`perm_dossier_ver_data`, `direcciones_*`, `teleco_*`, `persona_nota_editar`, `lista_casas_posibles_periodo`, `sacd_ausencias_jefe_zona_data`, `ver_plan_sacd_data`…), y otros sacan avisos de PHP.
- **Por qué no ahora:** el frontend siempre manda esos parámetros; no hay fallo visible para el usuario.
- **Notas:** lista completa en la sección 27 del plan (recorrido de los controladores de lectura).

### E2E: más flujos y pantallas por formulario

- **Qué:** specs de flujos de escritura para los módulos con más flujos sin cubrir (inventario, misas, ubis, encargossacd, cambios, procesos) y una pasada «carga sin error» de las pantallas que no salen como enlace directo del menú (se abren por POST desde un formulario).
- **Por qué no ahora:** la fase 3 cubrió los flujos críticos que pedía el plan (personas, actividades, asistentes, notas, usuarios, subidas y un rol restringido).
- **Notas:** inventario y reglas en [`e2e/README.md`](../../e2e/README.md).

### E2E: la pila de navegación de la sesión del administrador pasa de un spec a otro

- **Qué:** los specs autenticados comparten la sesión PHP del administrador (global-setup), y con ella la pila de `$oPosicion`. Si `rol-restringido` daba de alta su usuario por `usuario_form.php?nuevo=1`, el spec siguiente (`usuarios-alta-baja`) guardaba la ficha de un usuario existente como un alta («nombre de usuario en uso»). Se evitó dando de alta ese usuario con SQL.
- **Por qué no ahora:** no se ha comprobado si un usuario real puede llegar a ese estado (abrir un alta, salir sin guardar y luego editar otro usuario); habría que reproducirlo a mano y mirar `ListNavSupport::restoreSelectionFromStackPost()` en `usuario_form.php`.
- **Notas:** si es un fallo de la aplicación, va con test E2E que abra primero un alta y luego edite.

### Limpieza de las marcas de dossier en los tests PHPUnit

- **Qué:** los tests de integración que crean personas (ids aleatorios de las factories) dejan filas en `d_dossiers_abiertos` cuando las borran (unas 280 de un solo día de ejecuciones); los specs E2E ya limpian las suyas.
- **Por qué no ahora:** no afecta a los resultados; hay que localizar qué tests generan esas marcas y añadir la limpieza.
- **Notas:** hallazgo 3 de la sección 28 del plan.

