---
tipo: "relacion_pantallas_api"
modulo: "configuracion"
pantallas: 4
endpoints_api: 6
capacidades: 4
estado_revision: "generado"
---

# Relacion Pantallas API - configuracion

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `configuracion.pantalla.modulos_form`

- Controller: `frontend/configuracion/controller/modulos_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/configuracion/modulos_form_data`

Capacidades:
- `configuracion.modulos.gestionar`

Endpoints aportados por capacidades:
- `/src/configuracion/modulos_update`

### `configuracion.pantalla.modulos_select`

- Controller: `frontend/configuracion/controller/modulos_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/configuracion/modulos_select_data`

Capacidades:
- `configuracion.modulos_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `configuracion.pantalla.modulos_update`

- Controller: `frontend/configuracion/controller/modulos_update.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/configuracion/modulos_update`

Capacidades:
- `configuracion.modulos.gestionar`

Endpoints aportados por capacidades:
- `/src/configuracion/modulos_form_data`

### `configuracion.pantalla.parametros`

- Controller: `frontend/configuracion/controller/parametros.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/configuracion/parametros_lista`

Capacidades:
- `configuracion.parametros.gestionar`

Endpoints aportados por capacidades:
- `/src/configuracion/parametros_update`

## Por Endpoint API

### `/src/configuracion/modulos_form_data`

Pantallas directas:
- `configuracion.pantalla.modulos_form`

Pantallas via capacidad:
- `configuracion.pantalla.modulos_update`

### `/src/configuracion/modulos_select_data`

Pantallas directas:
- `configuracion.pantalla.modulos_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/configuracion/modulos_update`

Pantallas directas:
- `configuracion.pantalla.modulos_update`

Pantallas via capacidad:
- `configuracion.pantalla.modulos_form`

### `/src/configuracion/parametros_lista`

Pantallas directas:
- `configuracion.pantalla.parametros`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/configuracion/parametros_update`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `configuracion.pantalla.parametros`

### `/src/configuracion/periodo_calendario_escolar_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/configuracion/parametros_update`
- `/src/configuracion/periodo_calendario_escolar_data`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
