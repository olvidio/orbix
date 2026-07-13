---
id: "planning.pantalla.planning_zones_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning por zonas SACD (calendario)"
controller: "frontend/planning/controller/planning_zones_select.php"
vistas: ["frontend/planning/view/planning_zones_select.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_zones_select_data"]
capacidades: ["planning.planning_zones_select.gestionar"]
campos: ["post.actividad", "post.id_zona", "post.modelo", "post.propuesta", "post.trimestre", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "revisado"
---

# Planning por zonas SACD (calendario)

Cuadrícula de actividades por zona SACD en el trimestre elegido. Fragmento AJAX desde `planning_zones_que`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_zones_select.php`

## Endpoints Usados

- `/src/planning/planning_zones_select_data`

## Acciones

- Exportar calendario
- Leyenda (`leyenda.php`)

## Ruta de menú

sin entrada de menú en el índice (fragmento del flujo por zonas)
