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

Nota historica: estas vistas quedaban en `apps/procesos/view/` mientras
`ActividadTipo::getHtml()` usara `core\ViewTwig`:

- `actividad_tipo_proceso.html.twig` (caso `tipoactiv-procesos`, retirado).
- `actividad_tipo_que_perm.html.twig` (casos `procesos`, `cambios`).
- `_actividad_tipo_proceso.js.html.twig` (retirado, era el JS del caso muerto).
- `_actividad_tipo.js.html.twig` (partial JS con `fnjs_actualizar_fases`).

Se mueven a `frontend/procesos/view/` en el slice 12.

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

- `frontend/actividades/view/actividades.js`: accion "proceso"
  apunta a `frontend/procesos/controller/actividad_proceso.php`.
- `frontend/actividades/controller/actividad_que.php`: `url_actualizar_fases`
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
- ~~Split del dispatcher por accion (`lista`, `update`, `get`).~~ **Hecho en slice 7.**

---

## Slice 6 - `usuario_perm_activ` + `usuario_perm_activ_ajax`

### Pantallas

- `usuario_perm_activ` (alta/edicion de permisos de actividad para un
  usuario/grupo). URL legacy
  `apps/procesos/controller/usuario_perm_activ.php` -> canonico
  `frontend/procesos/controller/usuario_perm_activ.php`.
- `usuario_perm_activ_ajax` (endpoint de refresco del desplegable
  `fase_ref` cuando cambia `id_tipo_activ` o `dl_propia`). URL legacy
  `apps/procesos/controller/usuario_perm_activ_ajax.php` -> canonico
  `/src/procesos/usuario_perm_activ_ajax`.

### Parametros de entrada (`POST`)

- `usuario_perm_activ`:
  - Via checkbox (desde `frontend/usuarios/view/perm_activ_lista.phtml`):
    `sel[0]` = `id_usuario#id_item#id_tipo_activ_txt#dl_propia`.
  - Via form directo: `id_usuario`, `id_tipo_activ_txt`, `dl_propia`.
  - `quien`, `que` se pasan tal cual a los camposHidden del hash.
- `usuario_perm_activ_ajax`: `id_tipo_activ`, `dl_propia`.

### Reglas funcionales

- `usuario_perm_activ` monta la ficha de permisos: `ActividadTipo`
  widget (legacy apps), desplegables `fase_ref[]`, `perm_off[]`,
  `perm_on[]` para cada `afecta_a` de `PermisosActividades::AFECTA`,
  cargando los permisos existentes via
  `PermUsuarioActividadRepository::getPermUsuarioActividades`. Calcula
  `perm_jefe` segun `oConfig->is_jefeCalendario()`, permisos `des` /
  `vcsd` (en sv) o `calendario`. El submit del form va al endpoint
  JSON existente `src/usuarios/perm_activ_guardar`.
- `usuario_perm_activ_ajax`: devuelve en `text/plain` el HTML de los
  `<option>` para el select `fase_ref[]` combinando
  `TipoDeActividadRepository::getTiposDeProcesos` y
  `ActividadFaseRepository::getArrayActividadFases`. No es un
  dispatcher multi-`que`; el endpoint es de accion unica.

### Backend nuevo

- Endpoint `/src/procesos/usuario_perm_activ_ajax`: port 1:1 del
  controlador legacy con `header('Content-Type: text/plain; charset=UTF-8')`.

### Frontend

- `frontend/procesos/controller/usuario_perm_activ.php`: copia del
  controlador legacy cambiando `ViewTwig` por `ViewNewTwig`, las
  `TRUE`/`FALSE` a `true`/`false`, e `url_actualizar` apuntando a
  `ConfigGlobal::getWeb() . '/src/procesos/usuario_perm_activ_ajax'`.
- `frontend/procesos/view/usuario_perm_activ.html.twig`: copia 1:1.

### Compatibilidad legacy

- `apps/procesos/controller/usuario_perm_activ.php`: wrapper al frontend.
- `apps/procesos/controller/usuario_perm_activ_ajax.php`: wrapper al src.
- Se elimina `apps/procesos/view/usuario_perm_activ.html.twig`.

### Referencias externas actualizadas

- `frontend/usuarios/view/perm_activ_lista.phtml`: las dos llamadas a
  `apps/procesos/controller/usuario_perm_activ.php` (nuevo y editar)
  apuntan ahora a `frontend/procesos/controller/usuario_perm_activ.php`.

### Pendiente futuro

- Migrar el JS inline de `usuario_perm_activ.html.twig` al patron
  `$.ajax` + JSON en lugar de asumir HTML plano como respuesta.
- Reemplazar `actividades\model\ActividadTipo` (legacy apps) cuando
  sea viable.

---

## Slice 7 - Split `fases_activ_cambio_ajax` en endpoints por accion

### Objetivo

Eliminar el dispatcher multi-`que` del flujo nuevo siguiendo el patron
descrito en `refactor.md` (seccion *Endpoints por accion*). El flujo
frontend ya no envia `que=...` sino que apunta directamente al
endpoint correspondiente.

### Casos de uso nuevos en `src/procesos/application/`

- `FasesActivCambioLista::execute(array $input): string`: construye la
  tabla HTML con actividades candidatas (incluye su propio `Posicion`
  para `recordar/setParametros`).
- `FasesActivCambioUpdate::execute(array $input): string`: aplica
  `setCompletado(t|f)` a las tareas de `id_fase_nueva` para cada
  `id_activ` seleccionado y devuelve errores si los hay.
- `FasesActivCambioGet::execute(array $input): string`: devuelve el
  `Desplegable` con las fases posibles para el tipo de actividad y la
  `dl_propia`.

### Endpoints HTTP nuevos

- `/src/procesos/fases_activ_cambio_lista` (text/plain).
- `/src/procesos/fases_activ_cambio_update` (text/plain).
- `/src/procesos/fases_activ_cambio_get` (text/plain).

### Dispatcher legacy

`src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_ajax.php`
queda como wrapper DEPRECADO: delega a los casos de uso en funcion de
`$_POST['que']`.

### Frontend

- `frontend/procesos/controller/fases_activ_cambio.php`: sustituye
  `url_ajax` unico por `url_lista`, `url_update`, `url_get`. Hashes
  (`h_lista`, `h_actualizar`) firman cada uno su URL destino.
  `CamposForm` ya no incluye `que` (implicito en la ruta).
- `frontend/procesos/view/fases_activ_cambio.html.twig`: `fnjs_lista`
  usa `url_lista`, `fnjs_cambiar` usa `url_update`, `fnjs_actualizar_fases`
  usa `url_get`. Los parametros JS ya no incluyen `que=...`.

### Pendiente futuro

- Cuando se confirme que nadie llama al dispatcher `fases_activ_cambio_ajax`,
  eliminar wrapper legacy y la ruta `/src/procesos/fases_activ_cambio_ajax`.

---

## Slice 8 - Split `actividad_proceso_ajax` en endpoints por accion

### Objetivo

Misma operacion que en slice 7 para el dispatcher de la pantalla de
proceso de una actividad. El formulario y los botones ya no envian
`que=generar|get|update`: cada boton pega directamente a su endpoint.

### Casos de uso nuevos en `src/procesos/application/`

- `ActividadProcesoGenerar::execute(array $input): string`: llama
  `ActividadProcesoTareaRepository::generarProceso($id_activ, sfsv, true)`.
- `ActividadProcesoGet::execute(array $input): string`: devuelve la tabla
  HTML con las fases/tareas del proceso de una actividad, respetando
  permisos de oficina del responsable.
- `ActividadProcesoUpdate::execute(array $input): string`: guarda
  completado/observ para un id_item concreto; devuelve texto de error
  si `ProcesoActividadService::guardar` falla.

### Endpoints HTTP nuevos

- `/src/procesos/actividad_proceso_generar` (text/plain).
- `/src/procesos/actividad_proceso_get` (text/plain).
- `/src/procesos/actividad_proceso_update` (text/plain).

### Dispatcher legacy

`actividad_proceso_ajax.php` queda como wrapper DEPRECADO que delega a
los casos de uso en funcion de `$_POST['que']`.

### Frontend

- `frontend/procesos/controller/actividad_proceso.php`: sustituye
  `url_ajax` unico por `url_generar`, `url_get`, `url_update`. Cada hash
  (`param_generar`, `param_actualizar`, `h_update`) firma su URL destino
  y ya no incluye `que` en los campos.
- `frontend/procesos/view/actividad_proceso.html.twig`: cada funcion
  JS (`fnjs_generar_proceso`, `fnjs_actualizar`, `fnjs_guardar`) apunta
  a la URL canonica correspondiente.

### Pendiente futuro

- Eliminar el wrapper `actividad_proceso_ajax` cuando no quede referencia
  activa (menus, codigo, sesiones).

---

## Slice 9 - Split `tipo_activ_proceso_ajax` en endpoints por accion

### Objetivo

Separar los tres `que` del dispatcher en endpoints canonicos siguiendo
el patron de `refactor.md`.

### Casos de uso nuevos en `src/procesos/application/`

- `TipoActivProcesoLista::execute(): string`: tabla HTML con todos los
  tipos de actividad y su proceso propio/no-propio asignado.
- `TipoActivProcesoLstPosibles::execute(array $input): string`: mini-tabla
  de procesos asignables para un id_tipo_activ concreto.
- `TipoActivProcesoAsignar::execute(array $input): string`: asigna un
  id_tipo_proceso al tipo de actividad, distinguiendo propio/no-propio.

### Endpoints HTTP nuevos

- `/src/procesos/tipo_activ_proceso_lista` (text/plain).
- `/src/procesos/tipo_activ_proceso_lst_posibles` (text/plain).
- `/src/procesos/tipo_activ_proceso_asignar` (text/plain).

### Dispatcher legacy

`tipo_activ_proceso_ajax.php` queda como wrapper DEPRECADO delegando a
los casos de uso via `$_POST['que']`.

### Frontend

- `frontend/procesos/controller/tipo_activ_proceso.php`: introduce
  `url_lista`, `url_lst_posibles`, `url_asignar` y hashes firmados por
  cada URL (sin `que` en los campos). El hash de la lista se normaliza
  a `&hnov=...` porque con `CamposForm` vacio `linkSinVal` devuelve
  `?hnov=...` (incompatible con body POST).
- `frontend/procesos/view/tipo_activ_proceso.html.twig`: cada funcion
  JS apunta directamente a la URL canonica.

### Pendiente futuro

- Retirar el wrapper legacy cuando ya no queden llamadas activas.

---

## Slice 10 - Split `procesos_ajax` en endpoints por accion

### Objetivo

`procesos_ajax` acumulaba siete `que` (`regenerar`, `clonar`, `get`,
`get_listado`, `depende`, `update`, `eliminar`) y una funcion muerta
`mover`. Se extrae cada accion a su propio caso de uso + endpoint siguiendo
el patron de `refactor.md`.

### Casos de uso nuevos en `src/procesos/application/`

- `ProcesosGet::execute(array $input): string`: arbol HTML con las fases y
  tareas del tipo de proceso. Incluye los helpers estaticos `dibujarTree`
  y `dibujarHijos` (antes eran funciones globales `procesos_dibujar_tree`
  y `procesos_dibujar_hijos` del dispatcher legacy).
- `ProcesosRegenerar::execute(array $input): string`: regenera las tareas
  de las actividades del tipo de proceso (insertar nuevas, eliminar las
  inexistentes). Delega la renderizacion final a `ProcesosGet`.
- `ProcesosClonar::execute(array $input): string`: clona todas las tareas
  del proceso de referencia sobre el tipo de proceso destino y retorna el
  arbol actualizado (llamando a `ProcesosGet`).
- `ProcesosGetListado::execute(array $input): string`: tabla plana con el
  listado de tareas del tipo de proceso.
- `ProcesosDepende::execute(array $input): string`: options HTML del
  desplegable de tareas (o fases) dependientes de una fase seleccionada.
- `ProcesosUpdate::execute(array $input): string`: actualiza una tarea
  (estado, oficina responsable, fase/tarea, fases/tareas previas y mensajes
  de requisito).
- `ProcesosEliminar::execute(array $input): string`: borra una tarea del
  proceso.

### Endpoints HTTP nuevos

- `/src/procesos/procesos_regenerar` (text/plain)
- `/src/procesos/procesos_clonar` (text/plain)
- `/src/procesos/procesos_get` (text/plain)
- `/src/procesos/procesos_get_listado` (text/plain)
- `/src/procesos/procesos_depende` (text/plain)
- `/src/procesos/procesos_update` (text/plain)
- `/src/procesos/procesos_eliminar` (text/plain)

### Dispatcher legacy

`procesos_ajax.php` queda como wrapper DEPRECADO delegando a los casos de
uso via `$_POST['que']`. Se mantiene la ruta para no romper referencias
que pudieran persistir durante la transicion.

### Frontend

- `frontend/procesos/controller/procesos_select.php`: sustituye el unico
  `url_ajax` por `url_regenerar`, `url_clonar`, `url_get`,
  `url_get_listado`, `url_update` y `url_eliminar`. Cada hash
  (`h_regenerar`, `h_get`, `h_get_listado`, `h_clonar`, `h_eliminar`)
  firma su URL destino y ya no incluye `que` en los campos. Se elimina
  el `h_mover` inutilizado.
- `frontend/procesos/controller/procesos_ver.php`: el formulario firma
  con `url_update` (quitando `que` de los campos hidden) y se anade un
  hash `h_depende` para el endpoint dependiente.
- `frontend/procesos/view/procesos_select.html.twig`: las funciones JS
  (`fnjs_regenerar`, `fnjs_clonar`, `fnjs_actualizar`, `fnjs_listado`,
  `fnjs_eliminar`, `fnjs_guardar`) apuntan a las URLs canonicas y sin
  `que=...`.
- `frontend/procesos/view/procesos_ver.html.twig`: `fnjs_get_depende` usa
  `url_depende` con su `h_depende` firmado.

### Pendiente futuro

- Retirar el wrapper `procesos_ajax` cuando no queden referencias activas.
- Limpiar el antiguo dispatcher y helpers en `apps/procesos/controller/`
  (ya no se usan desde el frontend migrado).

---

## Slice 11 - Retirada de wrappers legacy multi-`que`

### Objetivo

Tras verificar que ninguna vista, JS, controller vivo ni ruta referencia
los dispatchers multi-`que` heredados, se elimina la cadena de wrappers
deprecados introducida en los slices 7-10.

### Verificacion previa

Busqueda global de referencias activas a `procesos_ajax`,
`actividad_proceso_ajax`, `tipo_activ_proceso_ajax` y
`fases_activ_cambio_ajax`:

- Unicas coincidencias en codigo vivo: los propios wrappers auto-referenciandose
  en comentarios y la cadena `apps/ -> src/`.
- Resto: metadata de catalogos `.po`/`.pot`, documentacion (`*.md`) y un
  comentario historico en `frontend/actividades/controller/actividad_update.php`
  (linea 338, solo descriptivo).

### Archivos eliminados

Wrappers intermedios (`src/procesos/infrastructure/ui/http/controllers/`):

- `procesos_ajax.php`
- `actividad_proceso_ajax.php`
- `tipo_activ_proceso_ajax.php`
- `fases_activ_cambio_ajax.php`

Atajos historicos (`apps/procesos/controller/`):

- `procesos_ajax.php`
- `actividad_proceso_ajax.php`
- `tipo_activ_proceso_ajax.php`
- `fases_activ_cambio_ajax.php`

### Rutas retiradas

En `src/procesos/config/routes.php` se retiran las 4 rutas de los
dispatchers multi-`que`:

- `/src/procesos/procesos_ajax`
- `/src/procesos/actividad_proceso_ajax`
- `/src/procesos/tipo_activ_proceso_ajax`
- `/src/procesos/fases_activ_cambio_ajax`

Se conservan las rutas single-action `actividad_que_fases_ajax` y
`usuario_perm_activ_ajax` (no son dispatchers multi-`que`, se mantienen
como endpoints canonicos con su nombre actual hasta ulterior revision).

---

## Slice 12 - Migrar partials de `ActividadTipo` a `frontend/procesos/view`

### Objetivo

Sacar las vistas parciales que aun vivian bajo `apps/procesos/view/`
enlazadas desde `actividades\model\ActividadTipo::getHtml()`.

### Analisis de callers

- `tipoactiv-procesos`: nadie invoca `setPara('tipoactiv-procesos')`. El
  case y sus twigs (`actividad_tipo_proceso.html.twig`,
  `_actividad_tipo_proceso.js.html.twig`) son codigo muerto.
- `procesos` (usa `actividad_tipo_que_perm.html.twig` +
  `_actividad_tipo.js.html.twig`): llamado desde
  `frontend/procesos/controller/fases_activ_cambio.php` y
  `frontend/procesos/controller/usuario_perm_activ.php`.
- `cambios`: usa `apps/cambios/view/actividad_tipo_que_perm.html.twig`
  (fuera del modulo procesos, no se toca).

### Cambios

- `frontend/shared/model/ViewNewTwig::setAbsolutePath()` acepta paths
  absolutos y paths con prefijo `apps/` para poder compartir partials
  que aun viven en el arbol legacy via namespaces Twig.
- `apps/actividades/model/ActividadTipo::getHtml()`:
  - Elimina el caso muerto `tipoactiv-procesos`.
  - El caso `procesos` pasa a usar `ViewNewTwig('procesos/controller',
    ['actividades' => 'apps/actividades/view'])` para renderizar
    `actividad_tipo_que_perm.html.twig` desde `frontend/procesos/view`
    manteniendo el namespace `@actividades` apuntando al arbol legacy
    (aun contiene `_actividad_tipo_body.html.twig`).
- `frontend/procesos/view/actividad_tipo_que_perm.html.twig` y
  `frontend/procesos/view/_actividad_tipo.js.html.twig` son las nuevas
  ubicaciones canonicas.

### Archivos eliminados

En `apps/procesos/view/`:

- `actividad_tipo_proceso.html.twig` (huerfano tras retirar el case muerto).
- `_actividad_tipo_proceso.js.html.twig` (idem).
- `actividad_tipo_que_perm.html.twig` (movido a frontend).
- `_actividad_tipo.js.html.twig` (movido a frontend).

### Pendiente futuro

- Migrar `actividades\model\ActividadTipo` a `src/actividades/*` como
  caso de uso o servicio de renderizado (es la dependencia legacy que
  todavia vive en `apps/`).
- Evaluar si `apps/actividades/view/_actividad_tipo_body.html.twig` se
  puede mover a `frontend/actividades/view/` cuando se migren los
  controladores de `actividades` que aun dependen de `apps/actividades/view`.

---

## Slice 13 - Limpieza del patron `trigger("submit")` en el modulo procesos

### Objetivo

Retirar el ciclo `attr('action') + one("submit") + trigger("submit") +
off()` que sobrevivia en tres funciones JS del modulo procesos. El patron
mutaba el DOM del formulario y ocultaba la llamada real al endpoint.

### Cambios

Las tres funciones pasan a llamar `$.ajax({ url, type:'post',
data: $(formulario).serialize() })` directamente:

- `frontend/procesos/view/fases_activ_cambio.html.twig`:
  `fnjs_ver_activ`, `fnjs_cambiar`.
- `frontend/procesos/view/procesos_select.html.twig`: `fnjs_guardar`.

No se altera ninguna URL, hash ni contenido enviado: `$(formulario).serialize()`
cubre exactamente lo mismo que el submit diferido anterior.

### Verificacion

Busqueda en `frontend/procesos/` sin resultados para
`trigger("submit")` ni `attr('action')`.
