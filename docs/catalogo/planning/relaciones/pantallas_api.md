---
tipo: "relacion_pantallas_api"
modulo: "planning"
pantallas: 11
endpoints_api: 7
capacidades: 7
estado_revision: "generado"
---

# Relacion Pantallas API - planning

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `planning.pantalla.leyenda`

- Controller: `frontend/planning/controller/leyenda.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_casa_que`

- Controller: `frontend/planning/controller/planning_casa_que.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/planning/planning_casa_que_data`

Capacidades:
- `planning.planning_casa_que.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_casa_select`

- Controller: `frontend/planning/controller/planning_casa_select.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_casa_ver`

- Controller: `frontend/planning/controller/planning_casa_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/planning/planning_casa_ver_data`

Capacidades:
- `planning.planning_casa_ver.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_ctr_que`

- Controller: `frontend/planning/controller/planning_ctr_que.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_ctr_select`

- Controller: `frontend/planning/controller/planning_ctr_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/planning/planning_ctr_select_data`

Capacidades:
- `planning.planning_ctr_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_persona_que`

- Controller: `frontend/planning/controller/planning_persona_que.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_persona_select`

- Controller: `frontend/planning/controller/planning_persona_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/planning/planning_persona_select_data`

Capacidades:
- `planning.planning_persona_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_persona_ver`

- Controller: `frontend/planning/controller/planning_persona_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/planning/planning_persona_ver_data`

Capacidades:
- `planning.planning_persona_ver.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_zones_que`

- Controller: `frontend/planning/controller/planning_zones_que.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/planning/planning_zones_que_data`

Capacidades:
- `planning.planning_zones_que.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `planning.pantalla.planning_zones_select`

- Controller: `frontend/planning/controller/planning_zones_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/planning/planning_zones_select_data`

Capacidades:
- `planning.planning_zones_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/planning/planning_casa_que_data`

Pantallas directas:
- `planning.pantalla.planning_casa_que`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/planning/planning_casa_ver_data`

Pantallas directas:
- `planning.pantalla.planning_casa_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/planning/planning_ctr_select_data`

Pantallas directas:
- `planning.pantalla.planning_ctr_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/planning/planning_persona_select_data`

Pantallas directas:
- `planning.pantalla.planning_persona_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/planning/planning_persona_ver_data`

Pantallas directas:
- `planning.pantalla.planning_persona_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/planning/planning_zones_que_data`

Pantallas directas:
- `planning.pantalla.planning_zones_que`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/planning/planning_zones_select_data`

Pantallas directas:
- `planning.pantalla.planning_zones_select`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- Ninguno.

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
