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
