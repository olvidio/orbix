---
id: "planning.pantalla.planning_persona_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning por persona (calendario)"
controller: "frontend/planning/controller/planning_persona_ver.php"
vistas: ["frontend/planning/view/planning_persona_ver.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_persona_ver_data"]
capacidades: ["planning.planning_persona_ver.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.modelo", "post.obj_pau", "post.periodo", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "revisado"
---

# Planning por persona (calendario)

Calendario de actividades de las personas seleccionadas en `planning_persona_select`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_persona_ver.php`

## Endpoints Usados

- `/src/planning/planning_persona_ver_data`

## Acciones

- Exportar calendario
- Leyenda (`leyenda.php`)

## Ruta de menú

sin entrada de menú en el índice (fragmento del flujo por persona)
