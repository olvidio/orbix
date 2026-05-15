---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadtarifas"
titulo: "Tarifa Tipo Actividad"
pantalla: "actividadtarifas.pantalla.tarifa_tipo_actividad"
preguntas: ["Que se puede hacer en Tarifa Tipo Actividad?", "Que campos tiene Tarifa Tipo Actividad?", "Que acciones hay en Tarifa Tipo Actividad?"]
capacidades: ["actividadtarifas.relacion_tarifa.gestionar"]
endpoints: ["/src/actividadtarifas/relacion_tarifa_eliminar", "/src/actividadtarifas/relacion_tarifa_update"]
source: "docs/catalogo/actividadtarifas/pantallas/tarifa_tipo_actividad.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tarifa Tipo Actividad

## Resumen

Pantalla principal del modulo `actividadtarifas` - relacion `TipoTarifa` ↔ tipo de actividad (`RelacionTarifaTipoActividad`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `actividadtarifas.relacion_tarifa.gestionar`

## Endpoints Relacionados

- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
