---
tipo: "relacion_pantallas_api"
modulo: "shared"
pantallas: 2
endpoints_api: 6
capacidades: 6
estado_revision: "generado"
---

# Relacion Pantallas API - shared

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `shared.pantalla.tablaDB_formulario_ver`

- Controller: `frontend/shared/controller/tablaDB_formulario_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/shared/tablaDB_depende_datos`
- `/src/shared/tablaDB_formulario_datos`
- `/src/shared/tablaDB_lista_datos`
- `/src/shared/tablaDB_update`

Capacidades:
- `shared.tablaDB.gestionar`
- `shared.tablaDB_depende.gestionar`
- `shared.tablaDB_formulario.gestionar`
- `shared.tablaDB_lista.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `shared.pantalla.tablaDB_lista_ver`

- Controller: `frontend/shared/controller/tablaDB_lista_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos`
- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`

Capacidades:
- `shared.tablaDB_buscar.gestionar`
- `shared.tablaDB_lista.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/shared/locales_posibles`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/shared/tablaDB_buscar_datos`

Pantallas directas:
- `shared.pantalla.tablaDB_lista_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/shared/tablaDB_depende_datos`

Pantallas directas:
- `shared.pantalla.tablaDB_formulario_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/shared/tablaDB_formulario_datos`

Pantallas directas:
- `shared.pantalla.tablaDB_formulario_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/shared/tablaDB_lista_datos`

Pantallas directas:
- `shared.pantalla.tablaDB_formulario_ver`
- `shared.pantalla.tablaDB_lista_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/shared/tablaDB_update`

Pantallas directas:
- `shared.pantalla.tablaDB_formulario_ver`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/shared/locales_posibles`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
