---
id: "planning.pantalla.planning_casa_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "planning"
nombre: "Planning por casas (filtros)"
controller: "frontend/planning/controller/planning_casa_que.php"
vistas: ["frontend/planning/view/planning_casa_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_casa_select.php"]
endpoints: ["/src/planning/planning_casa_que_data"]
capacidades: ["planning.planning_casa_que.gestionar"]
campos: ["form.cdc_sel", "form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.id_cdc_mas", "form.id_cdc_num", "form.modelo", "form.periodo", "form.sin_activ", "form.year", "html.modelo", "html.sin_activ", "post.cdc_sel", "post.empiezamax", "post.empiezamin", "post.periodo", "post.propuesta_calendario", "post.sSeleccionados", "post.sin_activ", "post.stack", "post.year"]
acciones: ["fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_ver_planning"]
estado_revision: "revisado"
---

# Planning por casas (filtros)

Formulario de criterios: grupo de casas (`CasasQue`), periodo y opción de incluir casas sin actividad.
Al enviar navega a `planning_casa_select.php`.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/planning/controller/planning_casa_que.php`

## Vistas Relacionadas

- `frontend/planning/view/planning_casa_que.phtml`

## Campos

- `cdc_sel` + selección manual de casas (`id_cdc_mas`, `id_cdc_num`, `sSeleccionados`; `cdc_sel=9` exige casa)
- Periodo: `year`, `periodo`, `empiezamin`, `empiezamax`
- `sin_activ`: incluir casas sin actividad en el intervalo
- Hidden: `propuesta_calendario` (modo calendario en estudio)

Al cargar llama a `planning_casa_que_data` para filtrar el selector de casas según rol/permiso.

## Casos particulares

Alcance del selector vía `planning_casa_que_data`:

- Rol `PAU_CDC` → `modo_casas=casa` y `filtro.id_ubi_in`
- Permiso `des`/`vcsd` → `modo_casas=all`
- `mi_sfsv` 1 → `sv`; 2 → `sf`
- Resto → `all`

Con `propuesta_calendario` truthy y `year=0`, el front fuerza `year = año_actual+1`.

## Acciones

- Ver planning → `planning_casa_select.php`
- Comprobar fechas del periodo

## Manual De Usuario

Pantalla revisada contra `frontend/planning/` y `src/planning/`. Linaje `apps/planning/controller/planning_casa_que.php`.

## Ruta de menú

Variantes según `propuesta_calendario` en `_referencia_menus.md`:

- **Legacy (actual):** `dre/Calendario/scdl/… > planning > por casas` · `adl > Gestión casas > Planing Casas`
- **Pills2 (actual):** `ACTIVIDADES > Herramientas de calendario > Planning calendario actual` · `CASAS Y CTR > Gestión casas > Planing Casas`
- **Legacy (propuesta `propuesta_calendario=1`):** `adl|Calendario|dre > Nuevo Calendario > nuevo planing`
- **Pills2 (propuesta):** `ACTIVIDADES > Herramientas de calendario > Planning calendario en estudio`
