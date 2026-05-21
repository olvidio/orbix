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
estado_revision: "generado"
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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
