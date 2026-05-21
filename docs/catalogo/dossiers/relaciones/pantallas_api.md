---
tipo: "relacion_pantallas_api"
modulo: "dossiers"
pantallas: 4
endpoints_api: 6
capacidades: 5
estado_revision: "generado"
---

# Relacion Pantallas API - dossiers

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `dossiers.pantalla.dossiers_ver`

- Controller: `frontend/dossiers/controller/dossiers_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dossiers/dossiers_ver_pantalla_data`

Capacidades:
- `dossiers.dossiers_ver_pantalla.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dossiers.pantalla.lista_dossiers`

- Controller: `frontend/dossiers/controller/lista_dossiers.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dossiers/dossiers_lista_fichas_data`

Capacidades:
- `dossiers.dossiers_lista_fichas.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dossiers.pantalla.perm_dossier_ver`

- Controller: `frontend/dossiers/controller/perm_dossier_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dossiers/perm_dossier_ver_data`

Capacidades:
- `dossiers.perm_dossier_ver.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `dossiers.pantalla.perm_dossiers`

- Controller: `frontend/dossiers/controller/perm_dossiers.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/dossiers/perm_dossiers_data`

Capacidades:
- `dossiers.perm_dossiers.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/dossiers/dossiers_lista_fichas_data`

Pantallas directas:
- `dossiers.pantalla.lista_dossiers`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dossiers/dossiers_ver_pantalla_data`

Pantallas directas:
- `dossiers.pantalla.dossiers_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dossiers/perm_dossier_ver_data`

Pantallas directas:
- `dossiers.pantalla.perm_dossier_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dossiers/perm_dossiers_data`

Pantallas directas:
- `dossiers.pantalla.perm_dossiers`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dossiers/tipo_dossier_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/dossiers/tipo_dossier_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/dossiers/tipo_dossier_eliminar`, `tipo_dossier_guardar` — **resuelto**: `perm_dossier_ver.phtml` (fetch JSON).

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
