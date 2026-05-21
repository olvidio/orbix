---
id: "planning.pantalla.planning_ctr_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "planning"
nombre: "Planning Ctr Que"
controller: "frontend/planning/controller/planning_ctr_que.php"
vistas: ["frontend/planning/view/planning_ctr_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_ctr_select.php"]
endpoints: []
capacidades: []
campos: ["form.ctr", "form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.sacd", "form.year", "html.ctr", "html.modelo", "html.sacd", "html.todos_agd", "html.todos_n", "html.todos_s", "post.ctr", "post.empiezamax", "post.empiezamin", "post.obj_pau", "post.periodo", "post.sacd", "post.stack", "post.tipo", "post.todos_agd", "post.todos_n", "post.todos_s", "post.year"]
acciones: ["fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_ver_planning"]
estado_revision: "generado"
---

# Planning Ctr Que

Formulario de filtros para el planning por centros (personas de un centro determinado).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/planning/controller/planning_ctr_que.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_ctr_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/planning_ctr_select.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.ctr`
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.sacd`
- `form.year`
- `html.ctr`
- `html.modelo`
- `html.sacd`
- `html.todos_agd`
- `html.todos_n`
- `html.todos_s`
- `post.ctr`
- `post.empiezamax`
- `post.empiezamin`
- `post.obj_pau`
- `post.periodo`
- `post.sacd`
- `post.stack`
- `post.tipo`
- `post.todos_agd`
- `post.todos_n`
- `post.todos_s`
- `post.year`

## Acciones Detectadas

- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_ver_planning`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
