---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadtarifas"
titulo: "Tarifa"
pantalla: "actividadtarifas.pantalla.tarifa"
preguntas: ["Que se puede hacer en Tarifa?", "Que campos tiene Tarifa?", "Que acciones hay en Tarifa?"]
capacidades: ["actividadtarifas.tipo_tarifa.gestionar"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_eliminar", "/src/actividadtarifas/tipo_tarifa_lista_data", "/src/actividadtarifas/tipo_tarifa_update"]
source: "docs/catalogo/actividadtarifas/pantallas/tarifa.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tarifa

## Resumen

Pantalla principal del modulo `actividadtarifas` - catalogo de `TipoTarifa`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_tarifa`
- `form.letra`
- `form.modo`
- `form.observ`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- `actividadtarifas.tipo_tarifa.gestionar`

## Endpoints Relacionados

- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
