---
id: "pasarela.pantalla.nombre_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Nombre Form"
controller: "frontend/pasarela/controller/nombre_form.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/actividades/actividad_tipo_get", "/src/actividadtarifas/relacion_tarifa_update"]
capacidades: []
campos: ["form.entrada", "form.extendida", "form.iactividad_val", "form.iasistentes_val", "form.id_item", "form.id_tarifa", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv", "form.isfsv_val", "form.modo", "form.nombre_actividad", "form.opcion_sel", "form.salida", "post.id_item", "post.id_tipo_activ", "post.sactividad", "post.sasistentes", "post.snom_tipo"]
acciones: []
estado_revision: "generado"
---

# Nombre Form

Esta página muestra un formulario para asociar un nombre a un tipo de actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/nombre_form.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividades/actividad_tipo_get`
- `/src/actividadtarifas/relacion_tarifa_update`

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.entrada`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_item`
- `form.id_tarifa`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv`
- `form.isfsv_val`
- `form.modo`
- `form.nombre_actividad`
- `form.opcion_sel`
- `form.salida`
- `post.id_item`
- `post.id_tipo_activ`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
