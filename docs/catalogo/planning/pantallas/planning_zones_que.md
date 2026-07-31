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
campos: ["form.actividad", "form.id_zona", "form.trimestre", "form.year", "html.actividad", "html.id_zona", "html.trimestre", "post.actividad", "post.id_zona", "post.modo", "post.propuesta", "post.stack", "post.trimestre", "post.year"]
acciones: ["fnjs_enviar_formulario", "fnjs_ver_planning"]
estado_revision: "revisado"
---

# Planning por zonas SACD (filtros)

Formulario: zona SACD, trimestre/mes, año y si cargar actividades. Carga zonas permitidas vía
`planning_zones_que_data`. Al enviar abre `planning_zones_select`.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/planning/controller/planning_zones_que.php`

## Campos

- `id_zona` (zona concreta, o `todo` / `todo_propias` si el usuario es jefe de calendario)
- `trimestre` (1–4 trimestres, 5–6 semestres, 101–112 meses; por defecto según mes actual)
- `year`, `actividad` (`si`|`no`: cuadrícula con o sin actividades/ausencias)
- Hidden: `propuesta` (modo propuesta de calendario; distinto de `propuesta_calendario` de casas)
- `modo` en POST se propaga como `modelo` al select

## Casos particulares

- Rol `p-sacd` sin ser jefe de calendario → error de permiso en `planning_zones_que_data`.
- Jefe de calendario: opciones extra `id_zona=todo_propias` y `todo`.
- `actividad=si` carga actividades+ausencias; cualquier otro valor deja slots vacíos (cuadrícula limpia).
- Menú `?propuesta=true` fija el hidden; no confundir con `propuesta_calendario` del planning por casas.

## Acciones

- Ver planning → `planning_zones_select.php`

## Manual De Usuario

Revisado contra `frontend/planning/` y `src/planning/`. Plantilla PHTML (sin Twig).

## Ruta de menú

Variantes según `propuesta` en `_referencia_menus.md`:

- **Legacy:** `dre > planning > por zonas` · `vsr > planning > por zonas` · `exterior > sacd > Misas > Planning zonas`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > por zonas` · `dre/vsr/exterior > … > por zonas`
- Con `propuesta=true`: `dre > propuestas > planing zonas`
