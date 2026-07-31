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

- `ctr`: nombre de centro (vacío = todos con checkboxes de colectivo; UI: por defecto todos los n)
- `sacd`, `todos_n`, `todos_agd`, `todos_s`
- Periodo: `year`, `periodo`, `empiezamin`, `empiezamax`
- Hidden: `obj_pau`/`tipo` (el API `PlanningCtrSelectData` siempre usa `PersonaDl`; no los consume)

## Casos particulares

- Checkboxes `todos_n` / `todos_agd` / `todos_s`: en backend el último no vacío gana (mutuamente excluyentes).
- Si `ctr` vacío y ningún checkbox → default colectivo `n`.
- Filtro `sacd`: la API excluye sacd solo si `sacd === ''`; el radio «no» del form envía `0` (no excluye).

## Acciones

- Ver planning → `planning_ctr_select.php` (AJAX + `planning_ctr_select_data`)

## Manual De Usuario

Revisado contra `frontend/planning/` y `src/planning/`. Linaje `apps/planning/controller/planning_ctr_que.php`.

## Ruta de menú

- **Legacy:** `dre/Calendario/vest/vsm/… > planning > por centro` · `vsr/scdl > planning > por ctr`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Planning por ctr`
