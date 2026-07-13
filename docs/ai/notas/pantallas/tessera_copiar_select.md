---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Tessera Copiar Select"
pantalla: "notas.pantalla.tessera_copiar_select"
preguntas: ["Que se puede hacer en Tessera Copiar Select?", "Que campos tiene Tessera Copiar Select?", "Que acciones hay en Tessera Copiar Select?"]
capacidades: ["notas.tessera.gestionar", "notas.tessera_copiar_select.gestionar"]
endpoints: ["/src/notas/tessera_copiar", "/src/notas/tessera_copiar_select_data"]
source: "docs/catalogo/notas/pantallas/tessera_copiar_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tessera Copiar Select

## Resumen

Selección de persona destino para copiar tessera (mismo apellido).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_nom_dst`
- `html.copiar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_copiar`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `notas.tessera.gestionar`
- `notas.tessera_copiar_select.gestionar`

## Endpoints Relacionados

- `/src/notas/tessera_copiar`
- `/src/notas/tessera_copiar_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
