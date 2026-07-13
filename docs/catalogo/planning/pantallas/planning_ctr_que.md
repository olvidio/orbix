---
id: "planning.pantalla.planning_ctr_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "planning"
nombre: "Planning por centro (filtros)"
controller: "frontend/planning/controller/planning_ctr_que.php"
vistas: ["frontend/planning/view/planning_ctr_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_ctr_select.php"]
endpoints: []
capacidades: []
campos: ["form.ctr", "form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.sacd", "form.year", "html.ctr", "html.modelo", "html.sacd", "html.todos_agd", "html.todos_n", "html.todos_s", "post.ctr", "post.empiezamax", "post.empiezamin", "post.obj_pau", "post.periodo", "post.sacd", "post.stack", "post.tipo", "post.todos_agd", "post.todos_n", "post.todos_s", "post.year"]
acciones: ["fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_ver_planning"]
estado_revision: "revisado"
---

# Planning por centro (filtros)

Formulario: centro concreto o todos (`todos_n`/`todos_agd`/`todos_s`), periodo y filtro sacd.
Al enviar carga `planning_ctr_select` por AJAX.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/planning/controller/planning_ctr_que.php`

## Campos

- `ctr`: nombre de centro (vacío = todos con checkboxes de colectivo)
- `sacd`, `todos_n`, `todos_agd`, `todos_s`
- Periodo: `year`, `periodo`, `empiezamin`, `empiezamax`

## Acciones

- Ver planning → `planning_ctr_select.php` (AJAX + `planning_ctr_select_data`)

## Manual De Usuario

Revisado contra `frontend/planning/`. Linaje `apps/planning/controller/planning_ctr_que.php`.

## Ruta de menú

- **Legacy:** `dre > planning > por centro` (y equivalentes: `Calendario`, `vest`, `vsm`, …)
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Planning por ctr`
