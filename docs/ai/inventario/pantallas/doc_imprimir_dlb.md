---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Imprimir inventario DLB"
pantalla: "inventario.pantalla.doc_imprimir_dlb"
preguntas: ["Que se puede hacer en Imprimir inventario DLB?", "Que campos tiene Imprimir inventario DLB?", "Que acciones hay en Imprimir inventario DLB?"]
capacidades: ["inventario.inventario_css_inline.gestionar", "inventario.inventario_dlb.gestionar"]
endpoints: ["/src/inventario/inventario_css_inline_data", "/src/inventario/inventario_dlb"]
source: "docs/catalogo/inventario/pantallas/doc_imprimir_dlb.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Imprimir inventario DLB

## Resumen

Vista de impresión DLB vía `inventario_dlb`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.dl`
- `post.sel`

## Acciones Detectadas

- `fnjs_ver_equipaje`

## Capacidades Relacionadas

- `inventario.inventario_css_inline.gestionar`
- `inventario.inventario_dlb.gestionar`

## Endpoints Relacionados

- `/src/inventario/inventario_css_inline_data`
- `/src/inventario/inventario_dlb`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
