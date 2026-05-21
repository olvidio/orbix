---
id: actividadcargos.pantalla.select_cargos_de_actividad
tipo: pantalla
modulo: actividadcargos
subtipo: widget_dossier
id_tipo_dossier: 3102
codigo_dossier: cargos_de_actividad
estado_revision: revisado
---

# Widget — Relacion de cargos (actividad)

## Descripcion

Listado de personas con cargo en una actividad. Widget del dossier **3102**, renderizado por `Select_cargos_de_actividad::getHtml()`.

## Vista

- `frontend/actividadcargos/view/select_cargos_de_actividad.phtml`

## Endpoints (AJAX)

- `/src/actividadcargos/cargo_eliminar` — boton **quitar cargo**
- Formulario de edicion/alta via `DossierTipoPublicUrls::relativeFormController(3102)` → `form_cargos_de_actividad.php`

## Acciones usuario

- **modificar cargo** — una fila seleccionada → form modo `editar`
- **quitar cargo** — confirmacion; puede borrar asistente (permisos `des`/`vcsd`)
- Enlaces **añadir …** — alta segun tipo de persona permitido

## Modulo relacionado

- `dossiers`, `actividades`, `asistentes`
