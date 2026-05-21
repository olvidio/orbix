---
tipo: "relacion_pantallas_api"
modulo: "actividadcargos"
pantallas: 4
endpoints_api: 5
capacidades: 4
estado_revision: "revisado"
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

Endpoints sin pantalla directa detectada (generador no indexa `.phtml`):
- `/src/actividadcargos/cargo_editar` — formularios `form_cargos_*.phtml` (AJAX `fnjs_guardar_cargo_*`)
- `/src/actividadcargos/cargo_eliminar` — widgets `select_cargos_*.phtml` (AJAX `fnjs_borrar_cargo`)
- `/src/actividadcargos/cargo_nuevo` — formularios en modo `nuevo`

Pantallas documentadas manualmente:
- `actividadcargos.pantalla.select_cargos_de_actividad` (dossier 3102)
- `actividadcargos.pantalla.select_cargos_personas_en_actividad` (dossier 1302)

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
