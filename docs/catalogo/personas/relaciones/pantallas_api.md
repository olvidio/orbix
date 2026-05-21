---
tipo: "relacion_pantallas_api"
modulo: "personas"
pantallas: 6
endpoints_api: 9
capacidades: 7
estado_revision: "generado"
---

# Relacion Pantallas API - personas

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `personas.pantalla.home_persona`

- Controller: `frontend/personas/controller/home_persona.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/personas/home_persona_data`

Capacidades:
- `personas.home_persona.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `personas.pantalla.personas_editar`

- Controller: `frontend/personas/controller/personas_editar.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/personas/personas_editar_data`

Capacidades:
- `personas.personas_editar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `personas.pantalla.personas_que`

- Controller: `frontend/personas/controller/personas_que.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `personas.pantalla.personas_select`

- Controller: `frontend/personas/controller/personas_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/personas/personas_select_data`

Capacidades:
- `personas.personas_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `personas.pantalla.stgr_cambio`

- Controller: `frontend/personas/controller/stgr_cambio.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/personas/stgr_cambio_data`
- `/src/personas/stgr_update`

Capacidades:
- `personas.stgr.gestionar`
- `personas.stgr_cambio.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `personas.pantalla.traslado_form`

- Controller: `frontend/personas/controller/traslado_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

Capacidades:
- `personas.traslado.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/personas/home_persona_data`

Pantallas directas:
- `personas.pantalla.home_persona`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/persona_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/persona_update`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/personas_editar_data`

Pantallas directas:
- `personas.pantalla.personas_editar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/personas_select_data`

Pantallas directas:
- `personas.pantalla.personas_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/stgr_cambio_data`

Pantallas directas:
- `personas.pantalla.stgr_cambio`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/stgr_update`

Pantallas directas:
- `personas.pantalla.stgr_cambio`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/traslado_form_data`

Pantallas directas:
- `personas.pantalla.traslado_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/personas/traslado_update`

Pantallas directas:
- `personas.pantalla.traslado_form`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/personas/persona_eliminar`, `persona_update` — **resuelto**: `persona_form.phtml`, `personas_editar`, variantes STGR/SSS.

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
