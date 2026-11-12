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
