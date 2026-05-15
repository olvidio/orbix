---
tipo: "relacion_pantallas_api"
modulo: "actividadtarifas"
pantallas: 9
endpoints_api: 14
capacidades: 3
estado_revision: "generado"
---

# Relacion Pantallas API - actividadtarifas

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `actividadtarifas.pantalla.tarifa`

- Controller: `frontend/actividadtarifas/controller/tarifa.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

Capacidades:
- `actividadtarifas.tipo_tarifa.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/tipo_tarifa_form_data`

### `actividadtarifas.pantalla.tarifa_form`

- Controller: `frontend/actividadtarifas/controller/tarifa_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_update`

Capacidades:
- `actividadtarifas.tipo_tarifa.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_lista_data`

### `actividadtarifas.pantalla.tarifa_lista`

- Controller: `frontend/actividadtarifas/controller/tarifa_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadtarifas/tipo_tarifa_lista_data`

Capacidades:
- `actividadtarifas.tipo_tarifa.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_update`

### `actividadtarifas.pantalla.tarifa_tipo_actividad`

- Controller: `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_update`

Capacidades:
- `actividadtarifas.relacion_tarifa.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_lista_data`

### `actividadtarifas.pantalla.tarifa_tipo_actividad_form`

- Controller: `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividades/actividad_que_datos`
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_update`

Capacidades:
- `actividadtarifas.relacion_tarifa.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_lista_data`

### `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

- Controller: `frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadtarifas/relacion_tarifa_lista_data`

Capacidades:
- `actividadtarifas.relacion_tarifa.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_update`

### `actividadtarifas.pantalla.tarifa_ubi`

- Controller: `frontend/actividadtarifas/controller/tarifa_ubi.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_update`

Capacidades:
- `actividadtarifas.tarifa_ubi.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

### `actividadtarifas.pantalla.tarifa_ubi_form`

- Controller: `frontend/actividadtarifas/controller/tarifa_ubi_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_update`

Capacidades:
- `actividadtarifas.tarifa_ubi.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

### `actividadtarifas.pantalla.tarifa_ubi_lista`

- Controller: `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadtarifas/tarifa_ubi_lista_data`

Capacidades:
- `actividadtarifas.tarifa_ubi.gestionar`

Endpoints aportados por capacidades:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Por Endpoint API

### `/src/actividadtarifas/relacion_tarifa_eliminar`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_tipo_actividad`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_tipo_actividad_form`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

### `/src/actividadtarifas/relacion_tarifa_form_data`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_tipo_actividad_form`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_tipo_actividad`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

### `/src/actividadtarifas/relacion_tarifa_lista_data`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_tipo_actividad`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_form`

### `/src/actividadtarifas/relacion_tarifa_update`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_tipo_actividad`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_form`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

### `/src/actividadtarifas/tarifa_ubi_copiar`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_ubi`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_ubi_form`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

### `/src/actividadtarifas/tarifa_ubi_eliminar`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_ubi`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_ubi_form`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

### `/src/actividadtarifas/tarifa_ubi_form_data`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_ubi_form`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_ubi`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

### `/src/actividadtarifas/tarifa_ubi_lista_data`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_ubi_lista`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_ubi`
- `actividadtarifas.pantalla.tarifa_ubi_form`

### `/src/actividadtarifas/tarifa_ubi_update`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_ubi`
- `actividadtarifas.pantalla.tarifa_ubi_form`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_ubi_lista`

### `/src/actividadtarifas/tarifa_ubi_update_inc`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_ubi`
- `actividadtarifas.pantalla.tarifa_ubi_form`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

### `/src/actividadtarifas/tipo_tarifa_eliminar`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_form`
- `actividadtarifas.pantalla.tarifa_lista`

### `/src/actividadtarifas/tipo_tarifa_form_data`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa_form`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa`
- `actividadtarifas.pantalla.tarifa_lista`

### `/src/actividadtarifas/tipo_tarifa_lista_data`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa`
- `actividadtarifas.pantalla.tarifa_lista`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_form`

### `/src/actividadtarifas/tipo_tarifa_update`

Pantallas directas:
- `actividadtarifas.pantalla.tarifa`
- `actividadtarifas.pantalla.tarifa_form`

Pantallas via capacidad:
- `actividadtarifas.pantalla.tarifa_lista`

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/actividadtarifas/tarifa_ubi_update_inc`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno.

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
