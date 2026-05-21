---
id: actividadcargos.pantalla.select_cargos_personas_en_actividad
tipo: pantalla
modulo: actividadcargos
subtipo: widget_dossier
id_tipo_dossier: 1302
codigo_dossier: cargos_personas_en_actividad
estado_revision: revisado
---

# Widget — Relacion de cargos (persona)

## Descripcion

Listado de actividades en las que una persona tiene cargo. Widget del dossier **1302**, renderizado por `Select_cargos_personas_en_actividad::getHtml()`.

## Vista

- `frontend/actividadcargos/view/select_cargos_personas_en_actividad.phtml`

## Endpoints (AJAX)

- `/src/actividadcargos/cargo_eliminar`
- Formulario via dossier 1302 → `form_cargos_personas_en_actividad.php`

## Acciones usuario

- Filtro **actuales / curso / todos** (`BotonesCurso`)
- **modificar cargo** / **quitar cargo**
- Enlaces de alta por tipo de actividad (filas dl / otros)

## Modulo relacionado

- `dossiers`, `personas`, `actividades`
