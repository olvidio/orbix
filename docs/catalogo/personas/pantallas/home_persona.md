---
id: "personas.pantalla.home_persona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Cabecera de persona"
controller: "frontend/personas/controller/home_persona.php"
vistas: ["frontend/personas/view/home_persona.phtml"]
fragmentos_frontend: ["frontend/dossiers/helpers/DossiersListaRender.php"]
endpoints: ["/src/personas/home_persona_data"]
capacidades: ["personas.home_persona.gestionar"]
campos: ["post.id_nom", "post.id_tabla", "post.obj_pau", "post.sel"]
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Cabecera de persona

Resumen de datos básicos (nombre, dl, centro, STGR, teléfonos, e-mail, situación) y accesos a
ficha, dossiers y lista embebida de dossiers.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/home_persona.php`

## Endpoints Usados

- `/src/personas/home_persona_data`

## Manual De Usuario

Pantalla revisada contra `frontend/personas/`.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde listado `personas_select` o navegación con `id_nom`/`obj_pau`).
