---
id: "actividadplazas.pantalla.gestion_plazas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Gestion Plazas"
controller: "frontend/actividadplazas/controller/gestion_plazas.php"
vistas: ["frontend/actividadplazas/view/gestion_plazas.phtml"]
fragmentos_frontend: ["frontend/actividadplazas/controller/gestion_plazas.php"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
capacidades: ["actividadplazas.gestion_plazas.gestionar"]
campos: ["form.colName", "form.data", "html.refresh", "post.empiezamax", "post.empiezamin", "post.id_tipo_activ", "post.periodo", "post.refresh", "post.sactividad", "post.sactividad2", "post.sasistentes", "post.year"]
acciones: ["fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Gestion Plazas

Pantalla principal del modulo `actividadplazas`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/gestion_plazas.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/gestion_plazas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadplazas/controller/gestion_plazas.php`

## Endpoints Usados

- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Capacidades Relacionadas

- `actividadplazas.gestion_plazas.gestionar`

## Campos Detectados

- `form.colName`
- `form.data`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_tipo_activ`
- `post.periodo`
- `post.refresh`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
