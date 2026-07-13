---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadtarifas"
titulo: "Tarifa Ubi"
pantalla: "actividadtarifas.pantalla.tarifa_ubi"
preguntas: ["Que se puede hacer en Tarifa Ubi?", "Que campos tiene Tarifa Ubi?", "Que acciones hay en Tarifa Ubi?"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_copiar", "/src/actividadtarifas/tarifa_ubi_eliminar", "/src/actividadtarifas/tarifa_ubi_update"]
source: "docs/catalogo/actividadtarifas/pantallas/tarifa_ubi.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tarifa Ubi

## Resumen

Tarifas económicas por casa y año (`TarifaUbi`): filtros casa/año, listado AJAX, popup con HashB.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.cantidad`
- `form.id_item`
- `form.id_serie`
- `form.id_tarifa`
- `form.id_ubi`
- `form.letra`
- `form.year`
- `html.buscar`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_copiar_tarifas`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Endpoints Relacionados

- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
