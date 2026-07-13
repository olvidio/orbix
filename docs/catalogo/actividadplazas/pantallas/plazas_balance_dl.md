---
id: "actividadplazas.pantalla.plazas_balance_dl"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Plazas Balance Dl"
controller: "frontend/actividadplazas/controller/plazas_balance_dl.php"
vistas: ["frontend/actividadplazas/view/plazas_balance_dl.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadplazas/gestion_plazas_update", "/src/actividadplazas/plazas_balance_data"]
capacidades: ["actividadplazas.gestion_plazas.gestionar", "actividadplazas.plazas_balance.gestionar"]
campos: ["form.colName", "form.data", "post.dl", "post.id_tipo_activ"]
acciones: []
estado_revision: "revisado"
---

# Plazas Balance Dl

Devuelve el HTML del grid comparativo A vs B para insertarlo en `#comparativa` de `plazas_balance_que.phtml` (AJAX HTML).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/plazas_balance_dl.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/plazas_balance_dl.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadplazas/gestion_plazas_update`
- `/src/actividadplazas/plazas_balance_data`

## Capacidades Relacionadas

- `actividadplazas.gestion_plazas.gestionar`
- `actividadplazas.plazas_balance.gestionar`

## Campos Detectados

- `form.colName`
- `form.data`
- `post.dl`
- `post.id_tipo_activ`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Fragmento AJAX (no tiene menú propio): se carga dentro de `#comparativa` de la pantalla
`plazas_balance_que` cuando el usuario elige una delegación en el desplegable. Pinta la cabecera con
las concedidas cruzadas (A→B y B→A) y una `TablaEditable` con las concedidas y libres de cada
actividad en las dos delegaciones. Las celdas editables (las de mi dl) se guardan con doble clic
contra `gestion_plazas_update`.

## Ruta de menú

- Sin entrada de menú en el índice: es un fragmento invocado por `plazas_balance_que`.
