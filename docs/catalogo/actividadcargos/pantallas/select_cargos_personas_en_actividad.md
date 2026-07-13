---
id: actividadcargos.pantalla.select_cargos_personas_en_actividad
tipo: pantalla
modulo: actividadcargos
subtipo: widget_dossier
id_tipo_dossier: 1302
codigo_dossier: cargos_personas_en_actividad
estado_revision: "revisado"
---

# Widget — Relación de cargos (persona)

## Descripcion

Listado de actividades en las que una persona tiene cargo. Widget del dossier **1302**, renderizado por `Select_cargos_personas_en_actividad::getHtml()`.

## Vista

- `frontend/actividadcargos/view/select_cargos_personas_en_actividad.phtml`

## Endpoints (AJAX)

- `/src/actividadcargos/cargo_eliminar`
- Formulario vía dossier 1302 → `form_cargos_personas_en_actividad.php`

## Acciones usuario

- Filtro **actuales / curso / todos** (`BotonesCurso`, campo `modo_curso`)
- **modificar cargo** / **quitar cargo** (siempre visibles, también en ámbito `rstgr`)
- Enlaces de alta por tipo de actividad: **añadir cargo de la dl** (`aLinks_dl`) y **añadir cargo de otra dl** (`aLinks_otros`)

## Tabla

Columnas: cargo, actividad, ¿Puede ser agd?, observaciones.

## Modulo relacionado

- `dossiers`, `personas`, `actividades`

## Ruta de menú

- sin entrada de menú en el índice (widget embebido en dossier 1302 de la ficha de persona).
