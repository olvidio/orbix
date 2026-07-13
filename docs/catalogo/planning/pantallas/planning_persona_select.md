---
id: "planning.pantalla.planning_persona_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Listado de personas (planning)"
controller: "frontend/planning/controller/planning_persona_select.php"
vistas: ["frontend/planning/view/planning_persona_select.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/personas/controller/home_persona.php", "frontend/planning/controller/planning_persona_ver.php"]
endpoints: ["/src/planning/planning_persona_select_data"]
capacidades: ["planning.planning_persona_select.gestionar"]
campos: ["html.id_dossier", "html.modelo", "html.que", "post.apellido1", "post.apellido2", "post.centro", "post.empiezamax", "post.empiezamin", "post.id_sel", "post.na", "post.nombre", "post.obj_pau", "post.periodo", "post.scroll_id", "post.stack", "post.year"]
acciones: ["fnjs_actividades", "fnjs_enviar_formulario", "fnjs_planning_print", "fnjs_solo_uno", "fnjs_ver_planning"]
estado_revision: "revisado"
---

# Listado de personas (planning)

Tabla de personas que cumplen los filtros. Llama a `planning_persona_select_data` y permite ver el
planning de la selección o acceder a ficha/dossier.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_persona_select.php`

## Endpoints Usados

- `/src/planning/planning_persona_select_data`

## Acciones

- Ver planning → `planning_persona_ver` (una o varias personas)
- Imprimir / actividades / ficha persona / dossier

## Ruta de menú

sin entrada de menú en el índice (paso del flujo por persona)
