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

Cuadrícula de actividades por zona SACD en el trimestre/mes elegido. Fragmento AJAX desde `planning_zones_que`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_zones_select.php`

## Endpoints Usados

- `/src/planning/planning_zones_select_data`

## Casos particulares

- `propuesta` truthy → status ≠ borrable + banner «Propuesta de calendario…»; sin propuesta → solo `StatusId::ACTUAL` + permisos sacd.
- `actividad≠si` → filas de persona con slots vacíos (cuadrícula limpia).
- `id_zona=todo` / `todo_propias` resuelve el conjunto de zonas en el servicio.
- Trimestre 5 (2º semestre) puede cruzar de año en el cálculo de fechas.

## Acciones

- Exportar calendario
- Leyenda (`leyenda.php`)

## Ruta de menú

sin entrada de menú en el índice (fragmento del flujo por zonas)
