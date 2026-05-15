---
id: "actividadtarifas.pantalla.tarifa_tipo_actividad_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Tipo Actividad Form"
controller: "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividadtarifas/relacion_tarifa_form_data", "/src/actividadtarifas/relacion_tarifa_update"]
capacidades: ["actividadtarifas.relacion_tarifa.gestionar"]
campos: ["form.iactividad_val", "form.iasistentes_val", "form.id_item", "form.id_tarifa", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv_val", "post.id_item"]
acciones: []
estado_revision: "generado"
---

# Tarifa Tipo Actividad Form

Controlador AJAX HTML: form modificar/nuevo de `RelacionTarifaTipoActividad`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividades/actividad_que_datos`
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_update`

## Capacidades Relacionadas

- `actividadtarifas.relacion_tarifa.gestionar`

## Campos Detectados

- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_item`
- `form.id_tarifa`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `post.id_item`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
