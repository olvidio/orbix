---
tipo: "relacion_pantallas_api"
modulo: "procesos"
pantallas: 12
endpoints_api: 23
capacidades: 17
estado_revision: "generado"
---

# Relacion Pantallas API - procesos

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `procesos.pantalla.actividad_proceso`

- Controller: `frontend/procesos/controller/actividad_proceso.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_generar`
- `/src/procesos/actividad_proceso_get`
- `/src/procesos/actividad_proceso_update`

Capacidades:
- `procesos.actividad_proceso.gestionar`
- `procesos.actividad_proceso_generar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `procesos.pantalla.actividad_proceso_get`

- Controller: `frontend/procesos/controller/actividad_proceso_get.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/actividad_proceso_get`

Capacidades:
- `procesos.actividad_proceso.gestionar`

Endpoints aportados por capacidades:
- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_update`

### `procesos.pantalla.fases_activ_cambio`

- Controller: `frontend/procesos/controller/fases_activ_cambio.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividades/actividad_tipo_get`
- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_tipo_html`
- `/src/procesos/fases_activ_cambio_update`

Capacidades:
- `procesos.fases_activ_cambio.gestionar`
- `procesos.fases_activ_cambio_tipo_html.gestionar`

Endpoints aportados por capacidades:
- `/src/procesos/fases_activ_cambio_lista`

### `procesos.pantalla.fases_activ_cambio_lista`

- Controller: `frontend/procesos/controller/fases_activ_cambio_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/fases_activ_cambio_lista`

Capacidades:
- `procesos.fases_activ_cambio.gestionar`

Endpoints aportados por capacidades:
- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_update`

### `procesos.pantalla.procesos_get`

- Controller: `frontend/procesos/controller/procesos_get.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/procesos_get`

Capacidades:
- `procesos.procesos.gestionar`

Endpoints aportados por capacidades:
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_update`

### `procesos.pantalla.procesos_get_listado`

- Controller: `frontend/procesos/controller/procesos_get_listado.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/procesos_get_listado`

Capacidades:
- `procesos.procesos_get_listado.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `procesos.pantalla.procesos_select`

- Controller: `frontend/procesos/controller/procesos_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/procesos_clonar`
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_regenerar`
- `/src/procesos/procesos_select_data`
- `/src/procesos/procesos_update`

Capacidades:
- `procesos.procesos.gestionar`
- `procesos.procesos_clonar.gestionar`
- `procesos.procesos_regenerar.gestionar`
- `procesos.procesos_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `procesos.pantalla.procesos_ver`

- Controller: `frontend/procesos/controller/procesos_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/procesos_depende`
- `/src/procesos/procesos_update`
- `/src/procesos/procesos_ver_data`

Capacidades:
- `procesos.procesos.gestionar`
- `procesos.procesos_depende.gestionar`
- `procesos.procesos_ver.gestionar`

Endpoints aportados por capacidades:
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`

### `procesos.pantalla.tipo_activ_proceso`

- Controller: `frontend/procesos/controller/tipo_activ_proceso.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/procesos/tipo_activ_proceso_asignar`
- `/src/procesos/tipo_activ_proceso_lst_posibles`

Capacidades:
- `procesos.tipo_activ_proceso_asignar.gestionar`
- `procesos.tipo_activ_proceso_lst_posibles.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `procesos.pantalla.tipo_activ_proceso_lista`

- Controller: `frontend/procesos/controller/tipo_activ_proceso_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/tipo_activ_proceso_lista`

Capacidades:
- `procesos.tipo_activ_proceso.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `procesos.pantalla.tipo_activ_proceso_lst_posibles`

- Controller: `frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/tipo_activ_proceso_lst_posibles`

Capacidades:
- `procesos.tipo_activ_proceso_lst_posibles.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `procesos.pantalla.usuario_perm_activ`

- Controller: `frontend/procesos/controller/usuario_perm_activ.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/procesos/usuario_perm_activ_ajax`
- `/src/procesos/usuario_perm_activ_data`
- `/src/usuarios/perm_activ_guardar`

Capacidades:
- `procesos.usuario_perm_activ.gestionar`
- `procesos.usuario_perm_activ_ajax.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/procesos/actividad_proceso_data`

Pantallas directas:
- `procesos.pantalla.actividad_proceso`

Pantallas via capacidad:
- `procesos.pantalla.actividad_proceso_get`

### `/src/procesos/actividad_proceso_generar`

Pantallas directas:
- `procesos.pantalla.actividad_proceso`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/actividad_proceso_get`

Pantallas directas:
- `procesos.pantalla.actividad_proceso`
- `procesos.pantalla.actividad_proceso_get`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/actividad_proceso_update`

Pantallas directas:
- `procesos.pantalla.actividad_proceso`

Pantallas via capacidad:
- `procesos.pantalla.actividad_proceso_get`

### `/src/procesos/actividad_que_fases_ajax`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/fases_activ_cambio_get`

Pantallas directas:
- `procesos.pantalla.fases_activ_cambio`

Pantallas via capacidad:
- `procesos.pantalla.fases_activ_cambio_lista`

### `/src/procesos/fases_activ_cambio_lista`

Pantallas directas:
- `procesos.pantalla.fases_activ_cambio_lista`

Pantallas via capacidad:
- `procesos.pantalla.fases_activ_cambio`

### `/src/procesos/fases_activ_cambio_tipo_html`

Pantallas directas:
- `procesos.pantalla.fases_activ_cambio`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/fases_activ_cambio_update`

Pantallas directas:
- `procesos.pantalla.fases_activ_cambio`

Pantallas via capacidad:
- `procesos.pantalla.fases_activ_cambio_lista`

### `/src/procesos/procesos_clonar`

Pantallas directas:
- `procesos.pantalla.procesos_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/procesos_depende`

Pantallas directas:
- `procesos.pantalla.procesos_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/procesos_eliminar`

Pantallas directas:
- `procesos.pantalla.procesos_select`

Pantallas via capacidad:
- `procesos.pantalla.procesos_get`
- `procesos.pantalla.procesos_ver`

### `/src/procesos/procesos_get`

Pantallas directas:
- `procesos.pantalla.procesos_get`
- `procesos.pantalla.procesos_select`

Pantallas via capacidad:
- `procesos.pantalla.procesos_ver`

### `/src/procesos/procesos_get_listado`

Pantallas directas:
- `procesos.pantalla.procesos_get_listado`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/procesos_regenerar`

Pantallas directas:
- `procesos.pantalla.procesos_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/procesos_select_data`

Pantallas directas:
- `procesos.pantalla.procesos_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/procesos_update`

Pantallas directas:
- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_ver`

Pantallas via capacidad:
- `procesos.pantalla.procesos_get`

### `/src/procesos/procesos_ver_data`

Pantallas directas:
- `procesos.pantalla.procesos_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/tipo_activ_proceso_asignar`

Pantallas directas:
- `procesos.pantalla.tipo_activ_proceso`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/tipo_activ_proceso_lista`

Pantallas directas:
- `procesos.pantalla.tipo_activ_proceso_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/tipo_activ_proceso_lst_posibles`

Pantallas directas:
- `procesos.pantalla.tipo_activ_proceso`
- `procesos.pantalla.tipo_activ_proceso_lst_posibles`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/usuario_perm_activ_ajax`

Pantallas directas:
- `procesos.pantalla.usuario_perm_activ`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/procesos/usuario_perm_activ_data`

Pantallas directas:
- `procesos.pantalla.usuario_perm_activ`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/procesos/actividad_que_fases_ajax`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
