---
tipo: "relacion_pantallas_api"
modulo: "actividadcargos"
pantallas: 2
endpoints_api: 5
capacidades: 4
estado_revision: "generado"
---

# Relacion Pantallas API - actividadcargos

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `actividadcargos.pantalla.form_cargos_de_actividad`

- Controller: `frontend/actividadcargos/controller/form_cargos_de_actividad.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadcargos/form_cargos_de_actividad_data`

Capacidades:
- `actividadcargos.form_cargos_de_actividad.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadcargos.pantalla.form_cargos_personas_en_actividad`

- Controller: `frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadcargos/form_cargos_personas_en_actividad_data`

Capacidades:
- `actividadcargos.form_cargos_personas_en_actividad.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/actividadcargos/cargo_editar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadcargos/cargo_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadcargos/cargo_nuevo`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadcargos/form_cargos_de_actividad_data`

Pantallas directas:
- `actividadcargos.pantalla.form_cargos_de_actividad`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadcargos/form_cargos_personas_en_actividad_data`

Pantallas directas:
- `actividadcargos.pantalla.form_cargos_personas_en_actividad`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/actividadcargos/cargo_editar`
- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

Endpoints sin pantalla directa ni capacidad relacionada:
- `/src/actividadcargos/cargo_editar`
- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
