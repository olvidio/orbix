---
id: "planning.pantalla.planning_casa_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning Casa Ver"
controller: "frontend/planning/controller/planning_casa_ver.php"
vistas: ["frontend/planning/view/planning_casa_ver.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_casa_ver_data"]
capacidades: ["planning.planning_casa_ver.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.modelo", "post.periodo", "post.propuesta_calendario", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "generado"
---

# Planning Casa Ver

Planning (calendario) de actividades de un grupo de casas en un periodo dado.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_casa_ver.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_casa_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/leyenda.php`

## Endpoints Usados

- `/src/planning/planning_casa_ver_data`

## Capacidades Relacionadas

- `planning.planning_casa_ver.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.periodo`
- `post.propuesta_calendario`
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
