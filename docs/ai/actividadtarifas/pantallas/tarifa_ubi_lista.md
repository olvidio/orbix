---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadtarifas"
titulo: "Tarifa Ubi Lista"
pantalla: "actividadtarifas.pantalla.tarifa_ubi_lista"
preguntas: ["Que se puede hacer en Tarifa Ubi Lista?", "Que campos tiene Tarifa Ubi Lista?", "Que acciones hay en Tarifa Ubi Lista?"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_lista_data"]
source: "docs/catalogo/actividadtarifas/pantallas/tarifa_ubi_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tarifa Ubi Lista

## Resumen

Controlador AJAX HTML: listado de `TarifaUbi` por casa y año.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_ubi`
- `post.year`

## Acciones Detectadas

- `fnjs_copiar_tarifas`
- `fnjs_modificar`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Endpoints Relacionados

- `/src/actividadtarifas/tarifa_ubi_lista_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
