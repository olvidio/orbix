# Baseline migracion modulo `procesos`

Seguimiento de la migracion de `apps/procesos` hacia `frontend/procesos` + `src/procesos` segun patron descrito en `refactor.md`.

## Inventario inicial

### Controladores en `apps/procesos/controller/`

| Fichero | Tipo | Pareja AJAX / vista | Slice |
|--------|------|----------------------|-------|
| `procesos_select.php` | Render | `procesos_ajax.php` + `procesos_select.html.twig` | 1 |
| `procesos_ver.php` | Render (form) | `procesos_ajax.php` + `procesos_ver.html.twig` | 2 |
| `procesos_ajax.php` | AJAX multi-`que` | - | 2 |
| `tipo_activ_proceso.php` | Render | `tipo_activ_proceso_ajax.php` + `tipo_activ_proceso.html.twig` + `_actividad_tipo_proceso.js.html.twig` + `actividad_tipo_proceso.html.twig` + `actividad_tipo_que_perm.html.twig` | 3 |
| `tipo_activ_proceso_ajax.php` | AJAX multi-`que` | - | 3 |
| `actividad_proceso.php` | Render | `actividad_proceso_ajax.php` + `actividad_proceso.html.twig` + `_actividad_tipo.js.html.twig` | 4 |
| `actividad_proceso_ajax.php` | AJAX multi-`que` | - | 4 |
| `actividad_que_fases_ajax.php` | AJAX | - | 4 |
| `fases_activ_cambio.php` | Render | `fases_activ_cambio_ajax.php` + `fases_activ_cambio.html.twig` | 5 |
| `fases_activ_cambio_ajax.php` | AJAX multi-`que` | - | 5 |
| `usuario_perm_activ.php` | Render | `usuario_perm_activ_ajax.php` + `usuario_perm_activ.html.twig` | 6 |
| `usuario_perm_activ_ajax.php` | AJAX multi-`que` | - | 6 |

### Vistas en `apps/procesos/view/`

- `procesos_select.html.twig`
- `procesos_ver.html.twig`
- `tipo_activ_proceso.html.twig`
- `_actividad_tipo_proceso.js.html.twig`
- `actividad_tipo_proceso.html.twig`
- `actividad_tipo_que_perm.html.twig`
- `actividad_proceso.html.twig`
- `_actividad_tipo.js.html.twig`
- `fases_activ_cambio.html.twig`
- `usuario_perm_activ.html.twig`

---

## Slice 1 - `procesos_select`

### Pantalla

- URL legacy: `apps/procesos/controller/procesos_select.php`.
- Objetivo canonico: `frontend/procesos/controller/procesos_select.php`.

### Parametros de entrada (`POST`)

- `refresh` (int) - usa `Posicion::recordar`.
- `stack` (int) - si se navega por medio de `Posicion`, borra la ultima entrada y restaura parametros `id_sel`, `scroll_id`.

### Reglas funcionales

- Sin permisos ni `rstgr` especificos.
- Construye una pagina con selector de tipo de proceso (`oDespl`) a partir de `ProcesoTipoRepository::getArrayProcesoTipos()`.
- Exponer hashes (`h_actualizar`, `h_clonar`, `h_eliminar`, `h_nuevo`, `h_modificar`, `h_mover`) para que los `fnjs_*` del JS publiquen contra `procesos_ajax.php` / `procesos_ver.php`.
- Mientras los slices 2 y siguientes no aterricen, las URLs `url_ajax` y `url_ver` apuntan a los controladores legacy de `apps/` para no romper nada.

### Salida

- HTML renderizado por `procesos_select.html.twig` (div `div_modificar`, `overlay`, `div_buscar`, `div_proceso`, botones).

### Backend nuevo

- Caso de uso: `src\procesos\application\ProcesosSelectData::execute()` devuelve `['a_tipos_proceso' => array<int,string>]`.
- Endpoint: `/src/procesos/procesos_select_data` (GET y POST), `ContestarJson::enviar('', $data)`.

### Frontend

- `frontend/procesos/controller/procesos_select.php`: `PostRequest::getDataFromUrl('/src/procesos/procesos_select_data', [])`, construye `web\Desplegable`, `web\Hash` y renderiza con `frontend\shared\model\ViewNewTwig('procesos/controller')`.
- `frontend/procesos/view/procesos_select.html.twig`: copia de la vista legacy; URLs de `url_ver`/`url_ajax` provisionales hasta que caigan los slices 2-6; al terminar todos se cambiaran a rutas `frontend/...`.

### Compatibilidad legacy

- `apps/procesos/controller/procesos_select.php`: wrapper minimo que hace `require` al controlador `frontend`. Marcar como deprecado en comentario.
- Se elimina `apps/procesos/view/procesos_select.html.twig` (pasa a `frontend/procesos/view/`).

### Menus/plantillas a actualizar

- `documentacion/Documentacion_Obix/menus.csv`
- `log/menus/comun.sql`
- `proves/aux_metamenus.csv`
- `documentacion/Documentacion_Obix/13. Sistema.md`
- `documentacion/Documentacion_Obix/procesos/mapa_procesos_select.md`

---

## Slice 2 - `procesos_ver` + `procesos_ajax`

### Pantallas

- `procesos_ver` (render form editar/nuevo de una fase del tipo de proceso).
  URL legacy `apps/procesos/controller/procesos_ver.php` -> canonico
  `frontend/procesos/controller/procesos_ver.php`.
- `procesos_ajax` (dispatcher AJAX multi-`que`: `regenerar`, `clonar`,
  `get`, `get_listado`, `depende`, `update`, `eliminar`). URL legacy
  `apps/procesos/controller/procesos_ajax.php` -> canonico
  `/src/procesos/procesos_ajax`.

### Parametros de entrada (`POST`)

- `procesos_ver`: `mod` (`editar` / `nuevo`), `id_item`, `id_tipo_proceso`.
- `procesos_ajax`: `que` (accion) y parametros propios:
  - `regenerar` / `clonar` / `get` / `get_listado`: `id_tipo_proceso` (+ `id_tipo_proceso_ref` en clonar).
  - `depende`: `acc`, `valor_depende`.
  - `update`: `id_item`, `id_tipo_proceso`, `status`, `id_of_responsable`, `id_fase`, `id_tarea`, arrays `id_fase_previa[]`, `id_tarea_previa[]`, `mensaje_requisito[]`.
  - `eliminar`: `id_item`.

### Reglas funcionales

- `procesos_ver`: monta desplegables de fases, tareas, status, oficinas
  responsables y fases previas para que el usuario edite la fase del
  proceso. El submit del formulario va a `procesos_ajax` con `que=update`.
- `procesos_ajax`: salidas en `text/plain` (el JS las inyecta con
  `.done(rta_txt)`), conserva el dispatcher con `que` como wrapper
  transitorio segun `refactor.md`.

### Backend nuevo

- `src\procesos\application\ProcesosVerData::execute(mod, id_item)`:
  devuelve dropdown data serializable (`a_oficinas`, `a_status`,
  `a_fases`, `a_tareas`, `a_fases_previas` con sus tareas previas).
- Endpoint `/src/procesos/procesos_ver_data` (JSON via `ContestarJson`).
- Endpoint `/src/procesos/procesos_ajax` = port 1:1 del dispatcher
  legacy con `header('Content-Type: text/plain; charset=UTF-8')`. Se
  deja marcado como DEPRECADO en cabecera para recordar el split por
  accion pendiente.

### Frontend

- `frontend/procesos/controller/procesos_ver.php`: llama a
  `/src/procesos/procesos_ver_data`, construye `Desplegable`s y `Hash`
  con `setUrl` a `ConfigGlobal::getWeb() . '/src/procesos/procesos_ajax'`
  para que el form pueda postear directamente al src.
- `frontend/procesos/view/procesos_ver.html.twig`: copia 1:1 de la
  vista legacy.
- `frontend/procesos/controller/procesos_select.php` (slice 1): se
  actualizan `url_ajax` y `url_ver` para que apunten al src y al
  frontend migrado.

### Compatibilidad legacy

- `apps/procesos/controller/procesos_ver.php`: wrapper que hace
  `require` al controlador frontend.
- `apps/procesos/controller/procesos_ajax.php`: wrapper que hace
  `require` al controlador HTTP en `src/`.
- Se elimina `apps/procesos/view/procesos_ver.html.twig`.

### Pendiente futuro

- Split de `procesos_ajax` en endpoints por accion
  (`procesos_tree`, `procesos_listado`, `procesos_update`, `procesos_eliminar`, `procesos_depende`, `procesos_regenerar`, `procesos_clonar`)
  extrayendo la logica a clases dedicadas de `application/`.
- Fix del JS `fnjs_mas_dependencias` que referencia `aDesplFasesPrevia`
  (singular, inexistente) y del `fnjs_guardar` para migrar al patron
  `$.ajax(...)` sin `.trigger('submit')`.

---

## Slice 3 - `tipo_activ_proceso` + `tipo_activ_proceso_ajax`

### Pantallas

- `tipo_activ_proceso` (listado de tipos de actividad con su tipo de
  proceso asociado). URL legacy `apps/procesos/controller/tipo_activ_proceso.php`
  -> canonico `frontend/procesos/controller/tipo_activ_proceso.php`.
- `tipo_activ_proceso_ajax` (dispatcher AJAX multi-`que`:
  `lista`, `lst_posibles_procesos`, `asignar`). URL legacy
  `apps/procesos/controller/tipo_activ_proceso_ajax.php` -> canonico
  `/src/procesos/tipo_activ_proceso_ajax`.

### Parametros de entrada (`POST`)

- `tipo_activ_proceso`: no recibe parametros; solo prepara hashes.
- `tipo_activ_proceso_ajax`:
  - `lista`: sin parametros extra.
  - `lst_posibles_procesos`: `id_tipo_activ`, `propio`.
  - `asignar`: `id_tipo_activ`, `propio`, `id_tipo_proceso`.

### Reglas funcionales

- `tipo_activ_proceso`: render puro (solo hashes). El contenido se
  carga al `$(document).ready()` con `fnjs_lista()`.
- `tipo_activ_proceso_ajax`:
  - `lista` devuelve HTML con `web\Lista` + `TiposActividades` +
    `ProcesoTipoRepository`.
  - `lst_posibles_procesos` devuelve un mini-tabla con los posibles
    tipos de proceso filtrados por `mi_sfsv()`.
  - `asignar` actualiza el `id_tipo_proceso` (propio) o
    `id_tipo_proceso_ex` segun `propio`.
  - Salida siempre texto plano para `.done(rta_txt)`.

### Backend nuevo

- Endpoint `/src/procesos/tipo_activ_proceso_ajax`: port 1:1 del
  dispatcher con `header('Content-Type: text/plain; charset=UTF-8')`.
  Marcado como DEPRECADO en cabecera.

### Frontend

- `frontend/procesos/controller/tipo_activ_proceso.php`: monta
  `Hash`es con `setUrl` apuntando a la ruta src; no llama a
  `PostRequest` porque el render no necesita datos (las llamadas
  vienen via JS despues del `ready`).
- `frontend/procesos/view/tipo_activ_proceso.html.twig`: copia 1:1.

### Compatibilidad legacy

- `apps/procesos/controller/tipo_activ_proceso.php`: wrapper al frontend.
- `apps/procesos/controller/tipo_activ_proceso_ajax.php`: wrapper al src.
- Se elimina `apps/procesos/view/tipo_activ_proceso.html.twig`.

### Partial views NO migradas

Las siguientes vistas se usan desde `apps/actividades/model/ActividadTipo.php`
(metodo `render()`) con `core\ViewTwig`, que resuelve rutas bajo
`apps/<modulo>/view`. Mientras `ActividadTipo` no se migre, se mantienen
en `apps/procesos/view/`:

- `actividad_tipo_proceso.html.twig` (caso `tipoactiv-procesos`).
- `actividad_tipo_que_perm.html.twig` (casos `procesos`, `cambios`).
- `_actividad_tipo_proceso.js.html.twig` (partial JS).
- `_actividad_tipo.js.html.twig` (partial JS con `fnjs_actualizar_fases`).

Quedan pendientes para una fase en la que tambien se migre
`ActividadTipo` o cuando los controladores `actividad_proceso`,
`usuario_perm_activ` y `fases_activ_cambio` necesiten su propia copia
frontend (ver slices 4-6).

---

## Slice 4 - `actividad_proceso` + `actividad_proceso_ajax` + `actividad_que_fases_ajax`

### Pantallas

- `actividad_proceso` (panel con las fases del proceso de una actividad
  concreta). URL legacy `apps/procesos/controller/actividad_proceso.php`
  -> canonico `frontend/procesos/controller/actividad_proceso.php`.
- `actividad_proceso_ajax` (dispatcher AJAX multi-`que`:
  `generar`, `get`, `update`). URL legacy
  `apps/procesos/controller/actividad_proceso_ajax.php` -> canonico
  `/src/procesos/actividad_proceso_ajax`.
- `actividad_que_fases_ajax` (devuelve checkboxes HTML de fases para
  `fases_on` / `fases_off` en el form `actividad_que` de `actividades`).
  URL legacy `apps/procesos/controller/actividad_que_fases_ajax.php` ->
  canonico `/src/procesos/actividad_que_fases_ajax`.

### Parametros de entrada (`POST`)

- `actividad_proceso`: `id_activ` (o `sel[0]` con id#... desde checkbox).
- `actividad_proceso_ajax`:
  - `generar`: `id_activ`.
  - `get`: `id_activ`.
  - `update`: `id_item`, `completado`, `observ`, `force`.
- `actividad_que_fases_ajax`: `salida` (`fases_on` / `fases_off`),
  `id_tipo_activ`, `dl_propia`.

### Reglas funcionales

- `actividad_proceso` prepara hashes (`param_generar`, `param_actualizar`,
  `h_update`) y pinta la cabecera con nombre de la actividad y el boton
  regenerar (visible solo si el usuario tiene permiso `calendario`,
  `vcsd` o `des`). El contenido del proceso se carga al ready via
  `fnjs_actualizar()`.
- `actividad_proceso_ajax`:
  - `generar` regenera el proceso (`generarProceso($id_activ, mi_sfsv(), true)`).
  - `get` imprime la tabla HTML con checkbox completado, responsable,
    observ e input para guardar; filtra las filas segun permisos de
    oficina del responsable de cada tarea.
  - `update` actualiza el estado/observacion via `ProcesoActividadService::guardar`.
  - Salida en `text/plain`.
- `actividad_que_fases_ajax` devuelve un bloque HTML con inputs checkbox
  para todas las fases que tiene cualquiera de los tipos de proceso
  asociados al tipo de actividad (o a sus heredados).

### Backend nuevo

- Caso de uso: `src\procesos\application\ActividadProcesoData::execute(int $id_activ)`
  devuelve `['id_activ', 'nom_activ']`.
- Endpoint `/src/procesos/actividad_proceso_data`: JSON via `ContestarJson`.
- Endpoint `/src/procesos/actividad_proceso_ajax`: port 1:1 del
  dispatcher legacy con `header('Content-Type: text/plain; charset=UTF-8')`.
  Marcado como DEPRECADO.
- Endpoint `/src/procesos/actividad_que_fases_ajax`: port 1:1 del
  controlador legacy.

### Frontend

- `frontend/procesos/controller/actividad_proceso.php`: lee
  `nom_activ` desde `/src/procesos/actividad_proceso_data`, resuelve
  permiso de calendario en sesion, monta los `Hash`es apuntando a
  `ConfigGlobal::getWeb() . '/src/procesos/actividad_proceso_ajax'` y
  renderiza con `frontend\shared\model\ViewNewTwig('procesos/controller')`.
- `frontend/procesos/view/actividad_proceso.html.twig`: copia 1:1.

### Compatibilidad legacy

- `apps/procesos/controller/actividad_proceso.php`: wrapper al frontend.
- `apps/procesos/controller/actividad_proceso_ajax.php`: wrapper al src.
- `apps/procesos/controller/actividad_que_fases_ajax.php`: wrapper al src.
- `apps/procesos/view/actividad_proceso.html.twig`: eliminado (vive en `frontend/`).

### Referencias externas actualizadas

- `apps/actividades/controller/actividades.js`: accion "proceso"
  apunta a `frontend/procesos/controller/actividad_proceso.php`.
- `apps/actividades/controller/actividad_que.php`: `url_actualizar_fases`
  apunta a `/src/procesos/actividad_que_fases_ajax`.
- `apps/procesos/view/fases_activ_cambio.html.twig`: la llamada de
  `fnjs_ver_activ` apunta a `frontend/procesos/controller/actividad_proceso.php`.

### Pendiente futuro

- Split de `actividad_proceso_ajax` por accion (`generar`, `get`,
  `update`) con clases de `application/` dedicadas.
- Refactor del JS inline de `actividad_proceso.html.twig` para salir
  del patron `$.ajax` con hash inline y pasarse a endpoints JSON.

---

## Slice 5 - `fases_activ_cambio` + `fases_activ_cambio_ajax`

### Pantallas

- `fases_activ_cambio` (formulario para cambiar la fase a un grupo
  de actividades). URL legacy `apps/procesos/controller/fases_activ_cambio.php`
  -> canonico `frontend/procesos/controller/fases_activ_cambio.php`.
- `fases_activ_cambio_ajax` (dispatcher AJAX multi-`que`: `lista`,
  `update`, `get`). URL legacy
  `apps/procesos/controller/fases_activ_cambio_ajax.php` -> canonico
  `/src/procesos/fases_activ_cambio_ajax`.

### Parametros de entrada (`POST`)

- `fases_activ_cambio`: `dl_propia`, `id_fase_nueva`, `id_tipo_activ`,
  `sasistentes`, `sactividad`, `sactividad2`, `periodo`, `year`,
  `empiezamin`, `empiezamax`, `inicio`, `fin`. Opcionalmente `stack`
  para restaurar `Posicion` previa.
- `fases_activ_cambio_ajax`:
  - `lista`: `id_tipo_activ`, `dl_propia`, `id_fase_nueva`, `periodo`,
    `year`, `empiezamin`, `empiezamax`, `accion`.
  - `update`: `id_fase_nueva`, `sel[]`, `accion`.
  - `get`: `id_tipo_activ`, `dl_propia`, `id_fase_sel`.

### Reglas funcionales

- `fases_activ_cambio` prepara el widget `actividades\model\ActividadTipo`
  (legacy), el `PeriodoQue` y los `Hash`es (`h_lista`, `h_actualizar`,
  `h_tipo`) con URL al src. El JS del view hace `fnjs_actualizar_fases`
  en `ready`, dispara `fnjs_lista` tras cada cambio y usa
  `frontend/procesos/controller/actividad_proceso.php` para el boton
  "ver proceso actividad" (ya actualizado en slice 4).
- `fases_activ_cambio_ajax`:
  - `lista` construye una tabla HTML con actividades candidatas,
    marcando si cumplen los requisitos de la fase nueva; usa Posicion
    para recordar los filtros ante el back.
  - `update` aplica `setCompletado(t|f)` segun `accion` a la tarea de
    la fase nueva para cada `id_activ` seleccionado, respetando
    permisos de oficina del responsable.
  - `get` devuelve el `Desplegable` con las fases posibles para el
    `id_tipo_activ` actual y la `dl_propia`.
  - Salida siempre `text/plain`.

### Backend nuevo

- Endpoint `/src/procesos/fases_activ_cambio_ajax`: port 1:1 del
  dispatcher con `header('Content-Type: text/plain; charset=UTF-8')`.
  Marcado como DEPRECADO. Instancia su propio `web\Posicion` ya que
  los closures de FastRoute no heredan el `$oPosicion` global.

### Frontend

- `frontend/procesos/controller/fases_activ_cambio.php`: copia del
  controlador legacy cambiando `ViewTwig` por `ViewNewTwig` y los
  hashes apuntando a la ruta src via `ConfigGlobal::getWeb()`.
- `frontend/procesos/view/fases_activ_cambio.html.twig`: copia 1:1.

### Compatibilidad legacy

- `apps/procesos/controller/fases_activ_cambio.php`: wrapper al frontend.
- `apps/procesos/controller/fases_activ_cambio_ajax.php`: wrapper al src.
- Se elimina `apps/procesos/view/fases_activ_cambio.html.twig`.

### Menus / docs actualizados

- `documentacion/Documentacion_Obix/menus.csv`, `log/menus/comun.sql`,
  `proves/aux_metamenus.csv`: URL apunta a `frontend/procesos/controller/fases_activ_cambio.php`.
- Paginas de documentacion (2, 3, 8, 10, 12, 20) actualizadas.
- `documentacion/Documentacion_Obix/procesos/mapa_fases_activ_cambio.md`
  refleja la migracion a `frontend/` + `/src/procesos/fases_activ_cambio_ajax`.

### Pendiente futuro

- Seguir dependiendo de `actividades\model\ActividadTipo` (legacy apps)
  tanto en render como en fetch de fases; bloqueado hasta que se
  migre `ActividadTipo` al patron nuevo.
- Split del dispatcher por accion (`lista`, `update`, `get`).
