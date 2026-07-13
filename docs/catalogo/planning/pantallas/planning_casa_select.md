---
id: "planning.pantalla.planning_casa_select"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "planning"
nombre: "Selección de casas (planning)"
controller: "frontend/planning/controller/planning_casa_select.php"
vistas: ["frontend/planning/view/planning_casa_select.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php", "frontend/planning/controller/planning_casa_ver.php"]
endpoints: []
capacidades: []
campos: ["form.id_activ", "form.id_ubi", "post.cdc_sel", "post.empiezamax", "post.empiezamin", "post.id_cdc", "post.modelo", "post.periodo", "post.propuesta_calendario", "post.sin_activ", "post.year"]
acciones: ["fnjs_cambiar_activ", "fnjs_cerrar", "fnjs_nueva_activ", "fnjs_update_div", "fnjs_ver"]
estado_revision: "revisado"
---

# Selección de casas (planning)

Pantalla intermedia: lista las casas del grupo elegido y permite abrir el calendario, crear o
modificar actividades de casa (`planning_casa_nueva` / `planning_casa_modificar` en módulo actividades).

## Tipo

- Subtipo: `pantalla` (paso intermedio, no entrada de menú)
- Controller: `frontend/planning/controller/planning_casa_select.php`

## Acciones

- Ver planning → carga `planning_casa_ver` por AJAX
- Nueva / modificar actividad de casa
- Cambiar actividad asociada a una ubi

## Manual De Usuario

Sin endpoint `/src/` propio; propaga filtros de `planning_casa_que` vía POST.

## Ruta de menú

sin entrada de menú en el índice
