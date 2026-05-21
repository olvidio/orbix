---
id: "planning.pantalla.planning_persona_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning Persona Select"
controller: "frontend/planning/controller/planning_persona_select.php"
vistas: ["frontend/planning/view/planning_persona_select.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/personas/controller/home_persona.php", "frontend/planning/controller/planning_persona_ver.php"]
endpoints: ["/src/planning/planning_persona_select_data"]
capacidades: ["planning.planning_persona_select.gestionar"]
campos: ["html.id_dossier", "html.modelo", "html.que", "post.apellido1", "post.apellido2", "post.centro", "post.empiezamax", "post.empiezamin", "post.id_sel", "post.na", "post.nombre", "post.obj_pau", "post.periodo", "post.scroll_id", "post.stack", "post.year"]
acciones: ["fnjs_actividades", "fnjs_enviar_formulario", "fnjs_planning_print", "fnjs_solo_uno", "fnjs_ver_planning"]
estado_revision: "generado"
---

# Planning Persona Select

Lista de personas que cumplen los filtros del formulario anterior (`planning_persona_que`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_persona_select.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_persona_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`
- `frontend/personas/controller/home_persona.php`
- `frontend/planning/controller/planning_persona_ver.php`

## Endpoints Usados

- `/src/planning/planning_persona_select_data`

## Capacidades Relacionadas

- `planning.planning_persona_select.gestionar`

## Campos Detectados

- `html.id_dossier`
- `html.modelo`
- `html.que`
- `post.apellido1`
- `post.apellido2`
- `post.centro`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_sel`
- `post.na`
- `post.nombre`
- `post.obj_pau`
- `post.periodo`
- `post.scroll_id`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_actividades`
- `fnjs_enviar_formulario`
- `fnjs_planning_print`
- `fnjs_solo_uno`
- `fnjs_ver_planning`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
