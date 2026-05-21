---
tipo: "relacion_pantallas_api"
modulo: "ubiscamas"
pantallas: 5
endpoints_api: 9
capacidades: 5
estado_revision: "generado"
---

# Relacion Pantallas API - ubiscamas

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `ubiscamas.pantalla.cama_form`

- Controller: `frontend/ubiscamas/controller/cama_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/ubiscamas/cama_form_data`

Capacidades:
- `ubiscamas.cama.gestionar`

Endpoints aportados por capacidades:
- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_update`

### `ubiscamas.pantalla.habitacion_form`

- Controller: `frontend/ubiscamas/controller/habitacion_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/ubiscamas/habitacion_form_data`

Capacidades:
- `ubiscamas.habitacion.gestionar`

Endpoints aportados por capacidades:
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_update`

### `ubiscamas.pantalla.lista_habitaciones`

- Controller: `frontend/ubiscamas/controller/lista_habitaciones.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/ubiscamas/actividad_habitaciones_lista`

Capacidades:
- `ubiscamas.actividad_habitaciones.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `ubiscamas.pantalla.lista_habitaciones_distribucion`

- Controller: `frontend/ubiscamas/controller/lista_habitaciones_distribucion.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/ubiscamas/actividad_habitaciones_lista`

Capacidades:
- `ubiscamas.actividad_habitaciones.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `ubiscamas.pantalla.lista_habitaciones_nombres`

- Controller: `frontend/ubiscamas/controller/lista_habitaciones_nombres.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/ubiscamas/actividad_habitaciones_lista`

Capacidades:
- `ubiscamas.actividad_habitaciones.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/ubiscamas/actividad_habitaciones_lista`

Pantallas directas:
- `ubiscamas.pantalla.lista_habitaciones`
- `ubiscamas.pantalla.lista_habitaciones_distribucion`
- `ubiscamas.pantalla.lista_habitaciones_nombres`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/ubiscamas/cama_delete`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `ubiscamas.pantalla.cama_form`

### `/src/ubiscamas/cama_form_data`

Pantallas directas:
- `ubiscamas.pantalla.cama_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/ubiscamas/cama_update`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `ubiscamas.pantalla.cama_form`

### `/src/ubiscamas/habitacion_delete`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `ubiscamas.pantalla.habitacion_form`

### `/src/ubiscamas/habitacion_form_data`

Pantallas directas:
- `ubiscamas.pantalla.habitacion_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/ubiscamas/habitacion_update`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `ubiscamas.pantalla.habitacion_form`

### `/src/ubiscamas/update_cama_asistente`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/ubiscamas/update_solo_vip`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_update`
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_update`
- `/src/ubiscamas/update_cama_asistente`
- `/src/ubiscamas/update_solo_vip`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
