---
tipo: "relacion_pantallas_api"
modulo: "cartaspresentacion"
pantallas: 5
endpoints_api: 8
capacidades: 6
estado_revision: "generado"
---

# Relacion Pantallas API - cartaspresentacion

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `cartaspresentacion.pantalla.cartas_presentacion`

- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cartaspresentacion/cartas_presentacion_shell_data`

Capacidades:
- `cartaspresentacion.cartas_presentacion_shell.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cartaspresentacion.pantalla.cartas_presentacion_buscar`

- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cartaspresentacion/cartas_presentacion_buscar_data`

Capacidades:
- `cartaspresentacion.cartas_presentacion_buscar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cartaspresentacion.pantalla.cartas_presentacion_form`

- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cartaspresentacion/carta_presentacion_form_data`

Capacidades:
- `cartaspresentacion.carta_presentacion.gestionar`

Endpoints aportados por capacidades:
- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_update`

### `cartaspresentacion.pantalla.cartas_presentacion_lista`

- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

Capacidades:
- `cartaspresentacion.cartas_presentacion.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cartaspresentacion.pantalla.cartas_presentacion_ubis_lista`

- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cartaspresentacion/ubis_lista_data`

Capacidades:
- `cartaspresentacion.ubis.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/cartaspresentacion/carta_presentacion_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `cartaspresentacion.pantalla.cartas_presentacion_form`

### `/src/cartaspresentacion/carta_presentacion_form_data`

Pantallas directas:
- `cartaspresentacion.pantalla.cartas_presentacion_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cartaspresentacion/carta_presentacion_update`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `cartaspresentacion.pantalla.cartas_presentacion_form`

### `/src/cartaspresentacion/cartas_presentacion_buscar_data`

Pantallas directas:
- `cartaspresentacion.pantalla.cartas_presentacion_buscar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cartaspresentacion/cartas_presentacion_lista_data`

Pantallas directas:
- `cartaspresentacion.pantalla.cartas_presentacion_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cartaspresentacion/cartas_presentacion_shell_data`

Pantallas directas:
- `cartaspresentacion.pantalla.cartas_presentacion`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cartaspresentacion/poblaciones_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cartaspresentacion/ubis_lista_data`

Pantallas directas:
- `cartaspresentacion.pantalla.cartas_presentacion_ubis_lista`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/poblaciones_data`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
