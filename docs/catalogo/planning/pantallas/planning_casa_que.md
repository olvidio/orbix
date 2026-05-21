---
id: "planning.pantalla.planning_casa_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning Casa Que"
controller: "frontend/planning/controller/planning_casa_que.php"
vistas: ["frontend/planning/view/planning_casa_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_casa_select.php"]
endpoints: ["/src/planning/planning_casa_que_data"]
capacidades: ["planning.planning_casa_que.gestionar"]
campos: ["form.cdc_sel", "form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.id_cdc_mas", "form.id_cdc_num", "form.modelo", "form.periodo", "form.sin_activ", "form.year", "html.modelo", "html.sin_activ", "post.cdc_sel", "post.empiezamax", "post.empiezamin", "post.periodo", "post.propuesta_calendario", "post.sSeleccionados", "post.sin_activ", "post.stack", "post.year"]
acciones: ["fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_ver_planning"]
estado_revision: "generado"
---

# Planning Casa Que

Formulario de filtros para el planning por casas (se selecciona el grupo de casas y el periodo).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_casa_que.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_casa_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/planning_casa_select.php`

## Endpoints Usados

- `/src/planning/planning_casa_que_data`

## Capacidades Relacionadas

- `planning.planning_casa_que.gestionar`

## Campos Detectados

- `form.cdc_sel`
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.modelo`
- `form.periodo`
- `form.sin_activ`
- `form.year`
- `html.modelo`
- `html.sin_activ`
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.propuesta_calendario`
- `post.sSeleccionados`
- `post.sin_activ`
- `post.stack`
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
