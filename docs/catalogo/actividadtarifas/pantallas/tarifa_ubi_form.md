---
id: "actividadtarifas.pantalla.tarifa_ubi_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Ubi Form"
controller: "frontend/actividadtarifas/controller/tarifa_ubi_form.php"
vistas: ["frontend/actividadtarifas/view/tarifa_ubi_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_update"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
campos: ["html.cantidad", "html.ctx_eliminar", "html.ctx_update", "html.id_item", "html.id_ubi", "html.year", "post.id_item", "post.id_ubi", "post.letra", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_comprobar_dinero", "fnjs_guardar"]
estado_revision: "generado"
---

# Tarifa Ubi Form

Controlador AJAX HTML: form modificar/nuevo de `TarifaUbi`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_ubi_form.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa_ubi_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_update`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Campos Detectados

- `html.cantidad`
- `html.ctx_eliminar`
- `html.ctx_update`
- `html.id_item`
- `html.id_ubi`
- `html.year`
- `post.id_item`
- `post.id_ubi`
- `post.letra`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_comprobar_dinero`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
