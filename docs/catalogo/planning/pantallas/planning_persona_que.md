---
id: "planning.pantalla.planning_persona_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "planning"
nombre: "Planning por persona (filtros)"
controller: "frontend/planning/controller/planning_persona_que.php"
vistas: ["frontend/planning/view/planning_persona_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_persona_select.php"]
endpoints: []
capacidades: []
campos: ["form.apellido1", "form.apellido2", "form.centro", "form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.nombre", "form.periodo", "form.year", "html.apellido1", "html.apellido2", "html.btn_ok", "html.centro", "html.modelo", "html.nombre", "post.empiezamax", "post.empiezamin", "post.na", "post.obj_pau", "post.periodo", "post.stack", "post.year"]
acciones: ["fnjs_comprobar_fecha", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_ver_planning"]
estado_revision: "revisado"
---

# Planning por persona (filtros)

Criterios de búsqueda de personas y periodo. El colectivo (`obj_pau`, `na`) lo fija la entrada de menú.
Al enviar abre `planning_persona_select`.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/planning/controller/planning_persona_que.php`

## Campos

- Búsqueda: `nombre`, `apellido1`, `apellido2`, `centro`
- Periodo: `year`, `periodo`, `empiezamin`, `empiezamax`
- Hidden: `obj_pau`, `na` (según menú: `PersonaDl`, `PersonaSacd`, `PersonaEx`, …)

## Casos particulares

- `obj_pau` admitidos en front: `PersonaN|Nax|Agd|S|SSSC|Dl|Ex|Sacd` (otro valor → exit).
- El menú Pills2 puede pasar `es_sacd=1`; el módulo planning **no lo lee**. El colectivo lo fija `obj_pau` (+ `na` → `id_tabla=p{na}` en `PersonaEx`).
- Filtros legacy codificados: `saWhere` / `saWhereCtr`.

## Acciones

- Buscar → `planning_persona_select.php`

## Manual De Usuario

Revisado contra `frontend/planning/` y `src/planning/`.

## Ruta de menú

Variantes en `_referencia_menus.md`:

| Params | Legacy | Pills2 |
|--------|--------|--------|
| `obj_pau=PersonaDl` | `… > planning > persona r/dl` · `scdl > planning > persona dl` | `ACTIVIDADES > Herramientas de calendario > Plannig por personas` |
| `obj_pau=PersonaEx&na=a` | `scdl > planning > agd de paso` | `scdl > planning > agd de paso` |
| `obj_pau=PersonaEx&na=n` | `scdl > planning > num de paso` | `scdl > planning > num de paso` |
| `obj_pau=PersonaSacd` | `dre > planning > sacd r/dl` | — |
| `obj_pau=PersonaSacd&es_sacd=1` | — | `ACTIVIDADES > Herramientas de calendario > Plannig por personas sacd` |
