---
id: "planning.pantalla.planning_zones_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "planning"
nombre: "Planning por zonas SACD (filtros)"
controller: "frontend/planning/controller/planning_zones_que.php"
vistas: ["frontend/planning/view/planning_zones_que.phtml"]
fragmentos_frontend: ["frontend/planning/controller/planning_zones_select.php"]
endpoints: ["/src/planning/planning_zones_que_data"]
capacidades: ["planning.planning_zones_que.gestionar"]
campos: ["form.actividad", "form.id_zona", "form.trimestre", "form.year", "html.actividad", "html.id_zona", "html.trimestre", "post.actividad", "post.id_zona", "post.modo", "post.stack", "post.trimestre", "post.year"]
acciones: ["fnjs_enviar_formulario", "fnjs_ver_planning"]
estado_revision: "revisado"
---

# Planning por zonas SACD (filtros)

Formulario: zona SACD, trimestre, año y filtro de actividad. Carga zonas permitidas vía
`planning_zones_que_data`. Al enviar abre `planning_zones_select`.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/planning/controller/planning_zones_que.php`

## Campos

- `id_zona`, `trimestre`, `year`, `actividad`
- Hidden: `propuesta` (modo propuesta de calendario)

## Acciones

- Ver planning → `planning_zones_select.php`

## Manual De Usuario

Revisado contra `frontend/planning/`. Plantilla PHTML (sin Twig).

## Ruta de menú

Variantes según `propuesta` en `_referencia_menus.md`:

- **Legacy:** `dre > planning > por zonas` · `exterior > sacd > planning zonas`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > por zonas`
- Con `propuesta=true`: `dre > propuestas > planing zonas`
