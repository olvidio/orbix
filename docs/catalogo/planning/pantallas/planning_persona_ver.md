---
id: "planning.pantalla.planning_persona_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning Persona Ver"
controller: "frontend/planning/controller/planning_persona_ver.php"
vistas: ["frontend/planning/view/planning_persona_ver.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_persona_ver_data"]
capacidades: ["planning.planning_persona_ver.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.modelo", "post.obj_pau", "post.periodo", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "generado"
---

# Planning Persona Ver

Planning (calendario) de las actividades asignadas a un conjunto de personas seleccionadas en `planning_persona_select`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_persona_ver.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_persona_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/leyenda.php`

## Endpoints Usados

- `/src/planning/planning_persona_ver_data`

## Capacidades Relacionadas

- `planning.planning_persona_ver.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.obj_pau`
- `post.periodo`
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
