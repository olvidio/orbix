---
id: "planning.pantalla.planning_ctr_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning Ctr Select"
controller: "frontend/planning/controller/planning_ctr_select.php"
vistas: ["frontend/planning/view/planning_ctr_select.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_ctr_select_data"]
capacidades: ["planning.planning_ctr_select.gestionar"]
campos: ["post.ctr", "post.empiezamax", "post.empiezamin", "post.modelo", "post.periodo", "post.sacd", "post.tipo", "post.todos_agd", "post.todos_n", "post.todos_s", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "generado"
---

# Planning Ctr Select

Planning (calendario) de las personas de un centro (o grupo de centros), filtrado por periodo y tipo de persona (n, agd, s).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_ctr_select.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_ctr_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/leyenda.php`

## Endpoints Usados

- `/src/planning/planning_ctr_select_data`

## Capacidades Relacionadas

- `planning.planning_ctr_select.gestionar`

## Campos Detectados

- `post.ctr`
- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.periodo`
- `post.sacd`
- `post.tipo`
- `post.todos_agd`
- `post.todos_n`
- `post.todos_s`
- `post.year`

## Acciones Detectadas

- `fnjs_exportar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
