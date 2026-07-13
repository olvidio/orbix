---
id: "actividadtarifas.pantalla.tarifa_tipo_actividad_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Tipo Actividad Form"
controller: "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"
vistas: ["frontend/actividadtarifas/view/tarifa_tipo_actividad_form.html.twig", "frontend/actividadtarifas/view/tarifa_tipo_actividad_form_nuevo.html.twig"]
fragmentos_frontend: []
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividadtarifas/relacion_tarifa_form_data", "/src/actividadtarifas/relacion_tarifa_update"]
capacidades: ["actividadtarifas.relacion_tarifa.gestionar"]
campos: ["form.iactividad_val", "form.iasistentes_val", "form.id_item", "form.id_tarifa", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv_val", "post.id_item"]
acciones: []
estado_revision: "revisado"
---

# Tarifa Tipo Actividad Form

Fragmento AJAX: formulario popup modificar/nuevo de `RelacionTarifaTipoActividad` (twig).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa_tipo_actividad_form.html.twig` (edición)
- `frontend/actividadtarifas/view/tarifa_tipo_actividad_form_nuevo.html.twig` (alta)

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

Alta: bloque de búsqueda de tipo de actividad (`actividad_que_datos`) + desplegable de tarifa.
Edición: muestra nombre del tipo y desplegable de tarifa. Submit firmado con `HashFront`.

## Ruta de menú

Sin entrada propia; popup de `tarifa_tipo_actividad.php`.
