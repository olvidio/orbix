---
id: "actividadtarifas.pantalla.tarifa_tipo_actividad"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadtarifas"
nombre: "Tarifa Tipo Actividad"
controller: "frontend/actividadtarifas/controller/tarifa_tipo_actividad.php"
vistas: ["frontend/actividadtarifas/view/tarifa_tipo_actividad.phtml"]
fragmentos_frontend: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php"]
endpoints: ["/src/actividadtarifas/relacion_tarifa_eliminar", "/src/actividadtarifas/relacion_tarifa_update"]
capacidades: ["actividadtarifas.relacion_tarifa.gestionar"]
campos: ["form.id_item", "form.id_tarifa", "form.id_tipo_activ"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_id_activ", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Tarifa Tipo Actividad

Pantalla principal del modulo `actividadtarifas` - relacion `TipoTarifa` ↔ tipo de actividad (`RelacionTarifaTipoActividad`).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`

## Vistas Relacionadas

- `frontend/actividadtarifas/view/tarifa_tipo_actividad.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php`

## Endpoints Usados

- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_update`

## Capacidades Relacionadas

- `actividadtarifas.relacion_tarifa.gestionar`

## Campos Detectados

- `form.id_item`
- `form.id_tarifa`
- `form.id_tipo_activ`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_id_activ`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
