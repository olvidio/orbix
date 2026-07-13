---
id: "ubis.pantalla.ubis_editar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Ubis Editar"
controller: "frontend/ubis/controller/ubis_editar.php"
vistas: ["frontend/ubis/view/cdc_form.phtml", "frontend/ubis/view/ctrdl_form.phtml", "frontend/ubis/view/ctrex_form.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/ubis_eliminar.php", "frontend/ubis/controller/ubis_update.php"]
endpoints: ["/src/ubis/ubis_editar_data", "/src/ubis/ubis_editar_load_data"]
capacidades: ["ubis.ubis_editar.gestionar", "ubis.ubis_editar_load.gestionar"]
campos: ["html.active", "html.cdc", "html.n_buzon", "html.nombre_ubi", "html.num_cartas", "html.num_cartas_mensuales", "html.num_habit_indiv", "html.num_pi", "html.num_sacd", "html.observ", "html.plazas", "html.plazas_min", "html.que", "html.sf", "html.status", "html.sv", "html.tipo_ubi", "post.dl", "post.id_ubi", "post.nombre_ubi", "post.nuevo", "post.obj_pau", "post.region", "post.tipo_ubi"]
acciones: ["fnjs_eliminar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Ubis Editar

Formulario de edición o alta de ficha de centro o casa dentro de la ficha ubi.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/ubis/controller/ubis_editar.php`

## Vistas Relacionadas

- `frontend/ubis/view/cdc_form.phtml`
- `frontend/ubis/view/ctrdl_form.phtml`
- `frontend/ubis/view/ctrex_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/ubis_eliminar.php`
- `frontend/ubis/controller/ubis_update.php`

## Endpoints Usados

- `/src/ubis/ubis_editar_data`
- `/src/ubis/ubis_editar_load_data`

## Capacidades Relacionadas

- `ubis.ubis_editar.gestionar`
- `ubis.ubis_editar_load.gestionar`

## Campos Detectados

- `html.active`
- `html.cdc`
- `html.n_buzon`
- `html.nombre_ubi`
- `html.num_cartas`
- `html.num_cartas_mensuales`
- `html.num_habit_indiv`
- `html.num_pi`
- `html.num_sacd`
- `html.observ`
- `html.plazas`
- `html.plazas_min`
- `html.que`
- `html.sf`
- `html.status`
- `html.sv`
- `html.tipo_ubi`
- `post.dl`
- `post.id_ubi`
- `post.nombre_ubi`
- `post.nuevo`
- `post.obj_pau`
- `post.region`
- `post.tipo_ubi`

## Acciones Detectadas

- `fnjs_eliminar`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
