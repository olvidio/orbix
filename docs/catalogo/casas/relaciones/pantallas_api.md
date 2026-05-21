---
tipo: "relacion_pantallas_api"
modulo: "casas"
pantallas: 14
endpoints_api: 15
capacidades: 9
estado_revision: "generado"
---

# Relacion Pantallas API - casas

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `casas.pantalla.calendario_ubi_resumen`

- Controller: `frontend/casas/controller/calendario_ubi_resumen.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadtarifas/tarifa_ubi_update_inc`
- `/src/casas/calendario_ubi_resumen_data`
- `/src/ubis/casas_opciones_data`

Capacidades:
- `casas.calendario_ubi_resumen.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.calendario_ubi_resumen_body`

- Controller: `frontend/casas/controller/calendario_ubi_resumen_body.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/calendario_ubi_resumen_data`

Capacidades:
- `casas.calendario_ubi_resumen.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.casa`

- Controller: `frontend/casas/controller/casa.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_update`

Capacidades:
- `casas.casa_ingreso.gestionar`

Endpoints aportados por capacidades:
- `/src/casas/casa_ingreso_form_data`

### `casas.pantalla.casa_actividades_lista`

- Controller: `frontend/casas/controller/casa_actividades_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/casa_actividades_lista_data`

Capacidades:
- `casas.casa_actividades.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.casa_ec`

- Controller: `frontend/casas/controller/casa_ec.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.casa_ec_gastos_lista`

- Controller: `frontend/casas/controller/casa_ec_gastos_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/casa_ec_gastos_form_data`
- `/src/casas/casa_ec_gastos_guardar`

Capacidades:
- `casas.casa_ec_gastos.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.casa_ingreso_form`

- Controller: `frontend/casas/controller/casa_ingreso_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/casa_ingreso_form_data`

Capacidades:
- `casas.casa_ingreso.gestionar`

Endpoints aportados por capacidades:
- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_update`

### `casas.pantalla.casa_ingresos_lista`

- Controller: `frontend/casas/controller/casa_ingresos_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/casa_ingresos_lista_data`

Capacidades:
- `casas.casa_ingresos.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.casas_resumen`

- Controller: `frontend/casas/controller/casas_resumen.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.casas_resumen_lista`

- Controller: `frontend/casas/controller/casas_resumen_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/casas_resumen_data`

Capacidades:
- `casas.casas_resumen.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `casas.pantalla.grupo`

- Controller: `frontend/casas/controller/grupo.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_update`

Capacidades:
- `casas.grupo.gestionar`

Endpoints aportados por capacidades:
- `/src/casas/grupo_form_data`
- `/src/casas/grupo_lista_data`

### `casas.pantalla.grupo_form`

- Controller: `frontend/casas/controller/grupo_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/grupo_form_data`

Capacidades:
- `casas.grupo.gestionar`

Endpoints aportados por capacidades:
- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_lista_data`
- `/src/casas/grupo_update`

### `casas.pantalla.grupo_lista`

- Controller: `frontend/casas/controller/grupo_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/grupo_lista_data`

Capacidades:
- `casas.grupo.gestionar`

Endpoints aportados por capacidades:
- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_form_data`
- `/src/casas/grupo_update`

### `casas.pantalla.prevision_asistentes`

- Controller: `frontend/casas/controller/prevision_asistentes.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/casas/ingreso_plazas_previstas_update`
- `/src/casas/prevision_asistentes_data`

Capacidades:
- `casas.ingreso_plazas_previstas.gestionar`
- `casas.prevision_asistentes.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/casas/calendario_ubi_resumen_data`

Pantallas directas:
- `casas.pantalla.calendario_ubi_resumen`
- `casas.pantalla.calendario_ubi_resumen_body`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/casas/casa_actividades_lista_data`

Pantallas directas:
- `casas.pantalla.casa_actividades_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/casas/casa_ec_gastos_form_data`

Pantallas directas:
- `casas.pantalla.casa_ec_gastos_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/casas/casa_ec_gastos_guardar`

Pantallas directas:
- `casas.pantalla.casa_ec_gastos_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/casas/casa_ingreso_eliminar`

Pantallas directas:
- `casas.pantalla.casa`

Pantallas via capacidad:
- `casas.pantalla.casa_ingreso_form`

### `/src/casas/casa_ingreso_form_data`

Pantallas directas:
- `casas.pantalla.casa_ingreso_form`

Pantallas via capacidad:
- `casas.pantalla.casa`

### `/src/casas/casa_ingreso_update`

Pantallas directas:
- `casas.pantalla.casa`

Pantallas via capacidad:
- `casas.pantalla.casa_ingreso_form`

### `/src/casas/casa_ingresos_lista_data`

Pantallas directas:
- `casas.pantalla.casa_ingresos_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/casas/casas_resumen_data`

Pantallas directas:
- `casas.pantalla.casas_resumen_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/casas/grupo_eliminar`

Pantallas directas:
- `casas.pantalla.grupo`

Pantallas via capacidad:
- `casas.pantalla.grupo_form`
- `casas.pantalla.grupo_lista`

### `/src/casas/grupo_form_data`

Pantallas directas:
- `casas.pantalla.grupo_form`

Pantallas via capacidad:
- `casas.pantalla.grupo`
- `casas.pantalla.grupo_lista`

### `/src/casas/grupo_lista_data`

Pantallas directas:
- `casas.pantalla.grupo_lista`

Pantallas via capacidad:
- `casas.pantalla.grupo`
- `casas.pantalla.grupo_form`

### `/src/casas/grupo_update`

Pantallas directas:
- `casas.pantalla.grupo`

Pantallas via capacidad:
- `casas.pantalla.grupo_form`
- `casas.pantalla.grupo_lista`

### `/src/casas/ingreso_plazas_previstas_update`

Pantallas directas:
- `casas.pantalla.prevision_asistentes`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/casas/prevision_asistentes_data`

Pantallas directas:
- `casas.pantalla.prevision_asistentes`

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
