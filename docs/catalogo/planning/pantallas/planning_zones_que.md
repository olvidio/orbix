---
id: "planning.pantalla.planning_zones_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning Zones Que"
controller: "frontend/planning/controller/planning_zones_que.php"
vistas: ["frontend/planning/view/planning_zones_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_zones_select.php"]
endpoints: ["/src/planning/planning_zones_que_data"]
capacidades: ["planning.planning_zones_que.gestionar"]
campos: ["form.actividad", "form.id_zona", "form.trimestre", "form.year", "html.actividad", "html.id_zona", "html.trimestre", "post.actividad", "post.id_zona", "post.modo", "post.stack", "post.trimestre", "post.year"]
acciones: ["fnjs_enviar_formulario", "fnjs_ver_planning"]
estado_revision: "generado"
---

# Planning Zones Que

Formulario de filtros para el planning por zonas (sacd).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_zones_que.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_zones_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/planning_zones_select.php`

## Endpoints Usados

- `/src/planning/planning_zones_que_data`

## Capacidades Relacionadas

- `planning.planning_zones_que.gestionar`

## Campos Detectados

- `form.actividad`
- `form.id_zona`
- `form.trimestre`
- `form.year`
- `html.actividad`
- `html.id_zona`
- `html.trimestre`
- `post.actividad`
- `post.id_zona`
- `post.modo`
- `post.stack`
- `post.trimestre`
- `post.year`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_ver_planning`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
