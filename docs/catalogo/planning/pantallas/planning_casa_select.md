---
id: "planning.pantalla.planning_casa_select"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "planning"
nombre: "Planning Casa Select"
controller: "frontend/planning/controller/planning_casa_select.php"
vistas: ["frontend/planning/view/planning_casa_select.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php", "frontend/planning/controller/planning_casa_ver.php"]
endpoints: []
capacidades: []
campos: ["form.id_activ", "form.id_ubi", "post.cdc_sel", "post.empiezamax", "post.empiezamin", "post.id_cdc", "post.modelo", "post.periodo", "post.propuesta_calendario", "post.sin_activ", "post.year"]
acciones: ["fnjs_cambiar_activ", "fnjs_cerrar", "fnjs_nueva_activ", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Planning Casa Select

Pantalla intermedia entre `planning_casa_que` y `planning_casa_ver`.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/planning/controller/planning_casa_select.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_casa_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`
- `frontend/planning/controller/planning_casa_ver.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.id_activ`
- `form.id_ubi`
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.modelo`
- `post.periodo`
- `post.propuesta_calendario`
- `post.sin_activ`
- `post.year`

## Acciones Detectadas

- `fnjs_cambiar_activ`
- `fnjs_cerrar`
- `fnjs_nueva_activ`
- `fnjs_update_div`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
