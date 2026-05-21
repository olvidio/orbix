---
id: "planning.pantalla.planning_zones_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning Zones Select"
controller: "frontend/planning/controller/planning_zones_select.php"
vistas: ["frontend/planning/view/planning_zones_select.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_zones_select_data"]
capacidades: ["planning.planning_zones_select.gestionar"]
campos: ["post.actividad", "post.id_zona", "post.modelo", "post.propuesta", "post.trimestre", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "generado"
---

# Planning Zones Select

Planning (calendario) por zonas sacd.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_zones_select.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_zones_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/planning/controller/leyenda.php`

## Endpoints Usados

- `/src/planning/planning_zones_select_data`

## Capacidades Relacionadas

- `planning.planning_zones_select.gestionar`

## Campos Detectados

- `post.actividad`
- `post.id_zona`
- `post.modelo`
- `post.propuesta`
- `post.trimestre`
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
