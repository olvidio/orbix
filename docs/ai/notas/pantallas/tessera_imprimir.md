---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Tessera Imprimir"
pantalla: "notas.pantalla.tessera_imprimir"
preguntas: ["Que se puede hacer en Tessera Imprimir?", "Que campos tiene Tessera Imprimir?", "Que acciones hay en Tessera Imprimir?"]
capacidades: ["notas.tessera_imprimir.gestionar"]
endpoints: ["/src/notas/tessera_imprimir_data"]
source: "docs/catalogo/notas/pantallas/tessera_imprimir.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tessera Imprimir

## Resumen

Impresión HTML de tessera.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.cara`
- `post.id_nom`
- `post.id_tabla`
- `post.refresh`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_update_div`

## Capacidades Relacionadas

- `notas.tessera_imprimir.gestionar`

## Endpoints Relacionados

- `/src/notas/tessera_imprimir_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
