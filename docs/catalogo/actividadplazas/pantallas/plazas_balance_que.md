---
id: "actividadplazas.pantalla.plazas_balance_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Plazas Balance Que"
controller: "frontend/actividadplazas/controller/plazas_balance_que.php"
vistas: ["frontend/actividadplazas/view/plazas_balance_que.phtml"]
fragmentos_frontend: ["frontend/actividadplazas/controller/plazas_balance_dl.php"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
capacidades: ["actividadplazas.plazas_balance_que.gestionar"]
campos: ["form.dl", "form.id_tipo_activ", "post.id_tipo_activ", "post.sactividad", "post.sasistentes"]
acciones: ["fnjs_comparativa"]
estado_revision: "revisado"
---

# Plazas Balance Que

Pantalla de filtro para el balance de plazas entre dos dl: muestra un desplegable con las dl disponibles y un `#comparativa` vacio que se rellena via AJAX con `plazas_balance_dl.php` (frontend, devuelve HTML) al cambiar el valor del select.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/plazas_balance_que.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/plazas_balance_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadplazas/controller/plazas_balance_dl.php`

## Endpoints Usados

- `/src/actividadplazas/plazas_balance_que_data`

## Capacidades Relacionadas

- `actividadplazas.plazas_balance_que.gestionar`

## Campos Detectados

- `form.dl`
- `form.id_tipo_activ`
- `post.id_tipo_activ`
- `post.sactividad`
- `post.sasistentes`

## Acciones Detectadas

- `fnjs_comparativa`

## Manual De Usuario

Pantalla de filtro para el balance de plazas entre dos delegaciones. Muestra un desplegable con las
delegaciones disponibles; al seleccionar una (`fnjs_comparativa`) se carga por AJAX el grid
comparativo (`plazas_balance_dl`) dentro de `#comparativa`. Las opciones del desplegable y el
`id_tipo_activ` provienen de `plazas_balance_que_data`.

## Ruta de menú

- **Legacy:** vsm > ca > Balance de plazas (y variantes por perfil/tipo: dagd, vsg…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Balance plazas ca n entre r/dl (y variantes por tipo/colectivo)
