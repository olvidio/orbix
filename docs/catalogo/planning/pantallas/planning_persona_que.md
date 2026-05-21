---
id: "planning.pantalla.planning_persona_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "planning"
nombre: "Planning Persona Que"
controller: "frontend/planning/controller/planning_persona_que.php"
vistas: ["frontend/planning/view/planning_persona_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_persona_select.php"]
endpoints: []
capacidades: []
campos: ["form.apellido1", "form.apellido2", "form.centro", "form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.nombre", "form.periodo", "form.year", "html.apellido1", "html.apellido2", "html.btn_ok", "html.centro", "html.modelo", "html.nombre", "post.empiezamax", "post.empiezamin", "post.na", "post.obj_pau", "post.periodo", "post.stack", "post.year"]
acciones: ["fnjs_comprobar_fecha", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_ver_planning"]
estado_revision: "generado"
---

# Planning Persona Que

Formulario de filtros para el planning por persona (numerarios, agd, supernumerarios, sacd, de paso...).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/planning/controller/planning_persona_que.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_persona_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/planning_persona_select.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.apellido1`
- `form.apellido2`
- `form.centro`
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.nombre`
- `form.periodo`
- `form.year`
- `html.apellido1`
- `html.apellido2`
- `html.btn_ok`
- `html.centro`
- `html.modelo`
- `html.nombre`
- `post.empiezamax`
- `post.empiezamin`
- `post.na`
- `post.obj_pau`
- `post.periodo`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_comprobar_fecha`
- `fnjs_enviar`
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
