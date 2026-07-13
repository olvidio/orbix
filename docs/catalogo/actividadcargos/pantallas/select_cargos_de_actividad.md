---
id: actividadcargos.pantalla.select_cargos_de_actividad
tipo: pantalla
modulo: actividadcargos
subtipo: widget_dossier
id_tipo_dossier: 3102
codigo_dossier: cargos_de_actividad
estado_revision: "revisado"
---

# Widget — Relación de cargos (actividad)

## Descripcion

Listado de personas con cargo en una actividad. Widget del dossier **3102**, renderizado por `Select_cargos_de_actividad::getHtml()` e instanciado desde `dossiers_ver.php`.

## Vista

- `frontend/actividadcargos/view/select_cargos_de_actividad.phtml`

## Endpoints (AJAX)

- `/src/actividadcargos/cargo_eliminar` — botón **quitar cargo** (`fnjs_borrar_cargo`)
- Formulario de edición/alta vía `DossierTipoPublicUrls::relativeFormController(3102)` → `form_cargos_de_actividad.php`

## Acciones usuario

- **modificar cargo** — una fila seleccionada → form modo `editar` (`fnjs_mod_cargo`)
- **quitar cargo** — confirmación; puede borrar asistente (permisos `des`/`vcsd`, tipos `s`/`sg`)
- Enlaces **añadir …** — alta según tipo de persona permitido (`perm_pers_activ` del tipo de actividad)
- Refresco del dossier tras eliminar (`fnjs_actualizar` → `dossiers_ver.php` con `refresh=1`)

## Tabla

Columnas: cargo, nombre y apellidos, ¿Puede ser agd?, observaciones. Token `sel` por fila: `id_nom#id_item#elim_asis#id_schema`.

## Permisos

- Botones **modificar cargo** / **quitar cargo**: ocultos en ámbito `rstgr`.
- Enlaces de alta: según permisos por colectivo de persona en el tipo de actividad.
- Aviso de borrado de asistente al eliminar: usuarios con permiso oficina `des` o `vcsd`.

## Modulo relacionado

- `dossiers`, `actividades`, `asistentes`, `personas`

## Ruta de menú

- sin entrada de menú en el índice (widget embebido en dossier 3102 de la ficha de actividad; entrada habitual vía buscador de actividades, p. ej. **Legacy:** vsm > ca > buscar ca · **Pills2:** ACTIVIDADES > Buscar actividad > ca n).
