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
estado_revision: "revisado"
---

# Tarifa Tipo Actividad

Relación tarifa ↔ tipo de actividad (`RelacionTarifaTipoActividad`): listado, alta/edición en popup.

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

Listado al cargar (`fnjs_ver`). Alta abre formulario con selector de tipo de actividad
(`actividad_que_datos`) y tarifa; edición solo cambia la tarifa asignada. Mutaciones con `HashFront`
en el formulario (no HashB).

## Ruta de menú

- **Legacy:** adl > Tarifas > tarifa <-> tipo de actividad; dre/Calendario/exterior > Nuevo
  calendario > Tarifas > tarifa <-> tipo actividad.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Tarifa-tipo actividad.
