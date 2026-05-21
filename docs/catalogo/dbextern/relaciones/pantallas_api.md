---
tipo: "relacion_pantallas_api"
modulo: "dbextern"
pantallas: 7
endpoints_api: 16
capacidades: 16
estado_revision: "generado"
---

# Relacion Pantallas API - dbextern

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `dbextern.pantalla.sincro_index`

- Controller: `frontend/dbextern/controller/sincro_index.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dbextern/refrescar_bdu`
- `/src/dbextern/sincro_index_datos`
- `/src/dbextern/sincro_syncro`

Capacidades:
- `dbextern.refrescar_bdu.gestionar`
- `dbextern.sincro_index.gestionar`
- `dbextern.sincro_syncro.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dbextern.pantalla.ver_desaparecidos_de_listas`

- Controller: `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dbextern/sincro_baja`
- `/src/dbextern/ver_desaparecidos_de_listas_datos`

Capacidades:
- `dbextern.sincro_baja.gestionar`
- `dbextern.ver_desaparecidos_de_listas.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dbextern.pantalla.ver_desaparecidos_de_orbix`

- Controller: `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dbextern/sincro_desunir`
- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

Capacidades:
- `dbextern.sincro_desunir.gestionar`
- `dbextern.ver_desaparecidos_de_orbix.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dbextern.pantalla.ver_listas`

- Controller: `frontend/dbextern/controller/ver_listas.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dbextern/sincro_crear`
- `/src/dbextern/sincro_crear_todos`
- `/src/dbextern/sincro_unir`
- `/src/dbextern/ver_listas_datos`

Capacidades:
- `dbextern.sincro.gestionar`
- `dbextern.sincro_crear_todos.gestionar`
- `dbextern.sincro_unir.gestionar`
- `dbextern.ver_listas.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dbextern.pantalla.ver_orbix`

- Controller: `frontend/dbextern/controller/ver_orbix.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dbextern/sincro_unir`
- `/src/dbextern/ver_orbix_datos`

Capacidades:
- `dbextern.sincro_unir.gestionar`
- `dbextern.ver_orbix.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dbextern.pantalla.ver_orbix_otradl`

- Controller: `frontend/dbextern/controller/ver_orbix_otradl.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dbextern/sincro_trasladar_a`
- `/src/dbextern/ver_orbix_otradl_datos`

Capacidades:
- `dbextern.sincro_trasladar_a.gestionar`
- `dbextern.ver_orbix_otradl.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dbextern.pantalla.ver_traslados`

- Controller: `frontend/dbextern/controller/ver_traslados.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dbextern/sincro_trasladar`
- `/src/dbextern/ver_traslados_datos`

Capacidades:
- `dbextern.sincro_trasladar.gestionar`
- `dbextern.ver_traslados.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/dbextern/refrescar_bdu`

Pantallas directas:
- `dbextern.pantalla.sincro_index`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_baja`

Pantallas directas:
- `dbextern.pantalla.ver_desaparecidos_de_listas`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_crear`

Pantallas directas:
- `dbextern.pantalla.ver_listas`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_crear_todos`

Pantallas directas:
- `dbextern.pantalla.ver_listas`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_desunir`

Pantallas directas:
- `dbextern.pantalla.ver_desaparecidos_de_orbix`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_index_datos`

Pantallas directas:
- `dbextern.pantalla.sincro_index`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_syncro`

Pantallas directas:
- `dbextern.pantalla.sincro_index`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_trasladar`

Pantallas directas:
- `dbextern.pantalla.ver_traslados`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_trasladar_a`

Pantallas directas:
- `dbextern.pantalla.ver_orbix_otradl`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/sincro_unir`

Pantallas directas:
- `dbextern.pantalla.ver_listas`
- `dbextern.pantalla.ver_orbix`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/ver_desaparecidos_de_listas_datos`

Pantallas directas:
- `dbextern.pantalla.ver_desaparecidos_de_listas`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/ver_desaparecidos_de_orbix_datos`

Pantallas directas:
- `dbextern.pantalla.ver_desaparecidos_de_orbix`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/ver_listas_datos`

Pantallas directas:
- `dbextern.pantalla.ver_listas`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/ver_orbix_datos`

Pantallas directas:
- `dbextern.pantalla.ver_orbix`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/ver_orbix_otradl_datos`

Pantallas directas:
- `dbextern.pantalla.ver_orbix_otradl`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dbextern/ver_traslados_datos`

Pantallas directas:
- `dbextern.pantalla.ver_traslados`

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
