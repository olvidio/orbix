---
id: "actividadplazas.pantalla.resumen_plazas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Resumen Plazas"
controller: "frontend/actividadplazas/controller/resumen_plazas.php"
vistas: ["frontend/actividadplazas/view/resumen_plazas.phtml"]
fragmentos_frontend: ["frontend/actividadplazas/controller/resumen_plazas.php"]
endpoints: ["/src/actividadplazas/plazas_ceder", "/src/actividadplazas/resumen_plazas_data"]
capacidades: ["actividadplazas.plazas_ceder.gestionar", "actividadplazas.resumen_plazas.gestionar"]
campos: ["form.id_activ", "form.num_plazas", "form.region_dl", "html.btn_ok", "html.num_plazas", "html.refresh", "post.id_activ", "post.nom_activ", "post.sel"]
acciones: ["fnjs_actualizar", "fnjs_enviar_formulario", "fnjs_guardar"]
estado_revision: "generado"
---

# Resumen Plazas

Pantalla resumen de plazas por actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/resumen_plazas.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/resumen_plazas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadplazas/controller/resumen_plazas.php`

## Endpoints Usados

- `/src/actividadplazas/plazas_ceder`
- `/src/actividadplazas/resumen_plazas_data`

## Capacidades Relacionadas

- `actividadplazas.plazas_ceder.gestionar`
- `actividadplazas.resumen_plazas.gestionar`

## Campos Detectados

- `form.id_activ`
- `form.num_plazas`
- `form.region_dl`
- `html.btn_ok`
- `html.num_plazas`
- `html.refresh`
- `post.id_activ`
- `post.nom_activ`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
