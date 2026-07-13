---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Calendario Periodos"
pantalla: "ubis.pantalla.calendario_periodos"
preguntas: ["Que se puede hacer en Calendario Periodos?", "Que campos tiene Calendario Periodos?", "Que acciones hay en Calendario Periodos?"]
capacidades: ["ubis.calendario_periodos.gestionar"]
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
source: "docs/catalogo/ubis/pantallas/calendario_periodos.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Calendario Periodos

## Resumen

Pantalla principal de gestión de periodos de calendario de casas CDC por año.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_item`
- `form.id_ubi`
- `form.year`
- `html.buscar`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- `ubis.calendario_periodos.gestionar`

## Endpoints Relacionados

- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
