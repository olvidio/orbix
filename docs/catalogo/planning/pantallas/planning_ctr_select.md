---
id: "planning.pantalla.planning_ctr_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning por centro (calendario)"
controller: "frontend/planning/controller/planning_ctr_select.php"
vistas: ["frontend/planning/view/planning_ctr_select.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_ctr_select_data"]
capacidades: ["planning.planning_ctr_select.gestionar"]
campos: ["post.ctr", "post.empiezamax", "post.empiezamin", "post.modelo", "post.periodo", "post.sacd", "post.tipo", "post.todos_agd", "post.todos_n", "post.todos_s", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "revisado"
---

# Planning por centro (calendario)

Calendario de personas y actividades agrupadas por centro. Fragmento AJAX cargado desde `planning_ctr_que`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_ctr_select.php`

## Endpoints Usados

- `/src/planning/planning_ctr_select_data`

## Acciones

- Exportar calendario
- Leyenda (`leyenda.php`)

## Ruta de menú

sin entrada de menú en el índice (fragmento del flujo por centro)
