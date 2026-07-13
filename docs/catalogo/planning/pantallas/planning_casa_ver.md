---
id: "planning.pantalla.planning_casa_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "planning"
nombre: "Planning por casas (calendario)"
controller: "frontend/planning/controller/planning_casa_ver.php"
vistas: ["frontend/planning/view/planning_casa_ver.phtml"]
fragmentos_frontend: ["frontend/planning/controller/leyenda.php"]
endpoints: ["/src/planning/planning_casa_ver_data"]
capacidades: ["planning.planning_casa_ver.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.modelo", "post.periodo", "post.propuesta_calendario", "post.year"]
acciones: ["fnjs_exportar"]
estado_revision: "revisado"
---

# Planning por casas (calendario)

Cuadrícula de actividades por casa en el periodo elegido. Se carga por AJAX desde
`planning_casa_select` tras confirmar casas.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/planning/controller/planning_casa_ver.php`

## Endpoints Usados

- `/src/planning/planning_casa_ver_data`

## Acciones

- Exportar calendario
- Leyenda de colores (`leyenda.php`)

## Manual De Usuario

Fragmento sin entrada de menú directa; acceso vía flujo casas (`planning_casa_que` → `planning_casa_select`).

## Ruta de menú

sin entrada de menú en el índice (fragmento del flujo por casas)
