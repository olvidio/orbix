---
tipo: "relacion_pantallas_api"
modulo: "actividadplazas"
pantallas: 6
endpoints_api: 11
capacidades: 9
estado_revision: "generado"
---

# Relacion Pantallas API - actividadplazas

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `actividadplazas.pantalla.gestion_plazas`

- Controller: `frontend/actividadplazas/controller/gestion_plazas.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

Capacidades:
- `actividadplazas.gestion_plazas.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadplazas.pantalla.incorporar_peticion`

- Controller: `frontend/actividadplazas/controller/incorporar_peticion.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/actividadplazas/peticiones_incorporar`

Capacidades:
- `actividadplazas.peticiones_incorporar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadplazas.pantalla.peticiones_activ`

- Controller: `frontend/actividadplazas/controller/peticiones_activ.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadplazas/peticiones_activ_data`
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

Capacidades:
- `actividadplazas.peticiones.gestionar`
- `actividadplazas.peticiones_activ.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadplazas.pantalla.plazas_balance_dl`

- Controller: `frontend/actividadplazas/controller/plazas_balance_dl.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadplazas/gestion_plazas_update`
- `/src/actividadplazas/plazas_balance_data`

Capacidades:
- `actividadplazas.gestion_plazas.gestionar`
- `actividadplazas.plazas_balance.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadplazas/gestion_plazas_data`

### `actividadplazas.pantalla.plazas_balance_que`

- Controller: `frontend/actividadplazas/controller/plazas_balance_que.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadplazas/plazas_balance_que_data`

Capacidades:
- `actividadplazas.plazas_balance_que.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadplazas.pantalla.resumen_plazas`

- Controller: `frontend/actividadplazas/controller/resumen_plazas.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadplazas/plazas_ceder`
- `/src/actividadplazas/resumen_plazas_data`

Capacidades:
- `actividadplazas.plazas_ceder.gestionar`
- `actividadplazas.resumen_plazas.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/actividadplazas/gestion_plazas_data`

Pantallas directas:
- `actividadplazas.pantalla.gestion_plazas`

Pantallas via capacidad:
- `actividadplazas.pantalla.plazas_balance_dl`

### `/src/actividadplazas/gestion_plazas_update`

Pantallas directas:
- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.plazas_balance_dl`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/peticiones_activ_data`

Pantallas directas:
- `actividadplazas.pantalla.peticiones_activ`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/peticiones_eliminar`

Pantallas directas:
- `actividadplazas.pantalla.peticiones_activ`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/peticiones_guardar`

Pantallas directas:
- `actividadplazas.pantalla.peticiones_activ`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/peticiones_incorporar`

Pantallas directas:
- `actividadplazas.pantalla.incorporar_peticion`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/plazas_balance_data`

Pantallas directas:
- `actividadplazas.pantalla.plazas_balance_dl`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/plazas_balance_que_data`

Pantallas directas:
- `actividadplazas.pantalla.plazas_balance_que`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/plazas_ceder`

Pantallas directas:
- `actividadplazas.pantalla.resumen_plazas`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/posibles_propietarios_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadplazas/resumen_plazas_data`

Pantallas directas:
- `actividadplazas.pantalla.resumen_plazas`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/actividadplazas/posibles_propietarios_data` — **resuelto**: formularios **asistentes** (`FormActividadesDeUnaPersonaRender`, `FormAsistentesAUnaActividadRender`).

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
