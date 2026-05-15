---
id: "actividadtarifas.pantalla.tarifa_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Form"
controller: "frontend/actividadtarifas/controller/tarifa_form.php"
vistas: ["frontend/actividadtarifas/view/tarifa_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_update"]
capacidades: ["actividadtarifas.tipo_tarifa.gestionar"]
campos: ["html.id_tarifa", "html.letra", "html.observ", "post.id_tarifa"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "generado"
---

# Tarifa Form

Controlador AJAX HTML: formulario modificar/nuevo de `TipoTarifa`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_form.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Capacidades Relacionadas

- `actividadtarifas.tipo_tarifa.gestionar`

## Campos Detectados

- `html.id_tarifa`
- `html.letra`
- `html.observ`
- `post.id_tarifa`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
