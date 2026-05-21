---
tipo: "relacion_pantallas_api"
modulo: "zonassacd"
pantallas: 6
endpoints_api: 9
capacidades: 5
estado_revision: "generado"
---

# Relacion Pantallas API - zonassacd

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `zonassacd.pantalla.zona_ctr`

- Controller: `frontend/zonassacd/controller/zona_ctr.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/zonassacd/zona_ctr`

Capacidades:
- `zonassacd.zona_ctr.gestionar`

Endpoints aportados por capacidades:
- `/src/zonassacd/zona_ctr_lista`
- `/src/zonassacd/zona_ctr_update`

### `zonassacd.pantalla.zona_ctr_lista_ajax`

- Controller: `frontend/zonassacd/controller/zona_ctr_lista_ajax.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/zonassacd/zona_ctr_lista`

Capacidades:
- `zonassacd.zona_ctr.gestionar`

Endpoints aportados por capacidades:
- `/src/zonassacd/zona_ctr`
- `/src/zonassacd/zona_ctr_update`

### `zonassacd.pantalla.zona_ctr_update_ajax`

- Controller: `frontend/zonassacd/controller/zona_ctr_update_ajax.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/zonassacd/zona_ctr_update`

Capacidades:
- `zonassacd.zona_ctr.gestionar`

Endpoints aportados por capacidades:
- `/src/zonassacd/zona_ctr`
- `/src/zonassacd/zona_ctr_lista`

### `zonassacd.pantalla.zona_sacd`

- Controller: `frontend/zonassacd/controller/zona_sacd.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/misas/zona_sacd_datos_get`
- `/src/misas/zona_sacd_datos_put`
- `/src/zonassacd/zona_sacd`

Capacidades:
- `zonassacd.zona_sacd.gestionar`

Endpoints aportados por capacidades:
- `/src/zonassacd/zona_sacd_lista`
- `/src/zonassacd/zona_sacd_update`

### `zonassacd.pantalla.zona_sacd_lista_ajax`

- Controller: `frontend/zonassacd/controller/zona_sacd_lista_ajax.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/zonassacd/zona_sacd_lista`

Capacidades:
- `zonassacd.zona_sacd.gestionar`

Endpoints aportados por capacidades:
- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_update`

### `zonassacd.pantalla.zona_sacd_update_ajax`

- Controller: `frontend/zonassacd/controller/zona_sacd_update_ajax.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/zonassacd/zona_sacd_update`

Capacidades:
- `zonassacd.zona_sacd.gestionar`

Endpoints aportados por capacidades:
- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_lista`

## Por Endpoint API

### `/src/zonassacd/zona_ctr`

Pantallas directas:
- `zonassacd.pantalla.zona_ctr`

Pantallas via capacidad:
- `zonassacd.pantalla.zona_ctr_lista_ajax`
- `zonassacd.pantalla.zona_ctr_update_ajax`

### `/src/zonassacd/zona_ctr_ajax`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/zonassacd/zona_ctr_lista`

Pantallas directas:
- `zonassacd.pantalla.zona_ctr_lista_ajax`

Pantallas via capacidad:
- `zonassacd.pantalla.zona_ctr`
- `zonassacd.pantalla.zona_ctr_update_ajax`

### `/src/zonassacd/zona_ctr_update`

Pantallas directas:
- `zonassacd.pantalla.zona_ctr_update_ajax`

Pantallas via capacidad:
- `zonassacd.pantalla.zona_ctr`
- `zonassacd.pantalla.zona_ctr_lista_ajax`

### `/src/zonassacd/zona_sacd`

Pantallas directas:
- `zonassacd.pantalla.zona_sacd`

Pantallas via capacidad:
- `zonassacd.pantalla.zona_sacd_lista_ajax`
- `zonassacd.pantalla.zona_sacd_update_ajax`

### `/src/zonassacd/zona_sacd_ajax`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/zonassacd/zona_sacd_lista`

Pantallas directas:
- `zonassacd.pantalla.zona_sacd_lista_ajax`

Pantallas via capacidad:
- `zonassacd.pantalla.zona_sacd`
- `zonassacd.pantalla.zona_sacd_update_ajax`

### `/src/zonassacd/zona_sacd_lista_tot`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/zonassacd/zona_sacd_update`

Pantallas directas:
- `zonassacd.pantalla.zona_sacd_update_ajax`

Pantallas via capacidad:
- `zonassacd.pantalla.zona_sacd`
- `zonassacd.pantalla.zona_sacd_lista_ajax`

## Alertas De Revision

Endpoints sin pantalla directa (legacy / migracion):
- `/src/zonassacd/zona_sacd_ajax` — ruta registrada; controller HTTP pendiente. Sustituido por `zona_sacd_lista` + `zona_sacd_update` via fragmentos AJAX.
- `/src/zonassacd/zona_ctr_ajax` — idem; sustituido por `zona_ctr_lista` + `zona_ctr_update`.
- `/src/zonassacd/zona_sacd_lista_tot` — menu **Lista sacd-zona**; JSON sin pantalla frontend migrada aun.

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
