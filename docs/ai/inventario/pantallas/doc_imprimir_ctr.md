---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Imprimir inventario centros"
pantalla: "inventario.pantalla.doc_imprimir_ctr"
preguntas: ["Que se puede hacer en Imprimir inventario centros?", "Que campos tiene Imprimir inventario centros?", "Que acciones hay en Imprimir inventario centros?"]
capacidades: ["inventario.inventario_css_inline.gestionar", "inventario.inventario_ctr.gestionar"]
endpoints: ["/src/inventario/inventario_css_inline_data", "/src/inventario/inventario_ctr"]
source: "docs/catalogo/inventario/pantallas/doc_imprimir_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Imprimir inventario centros

## Resumen

Vista de impresión: llama `inventario_ctr` + CSS inline.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.dl`
- `post.sel`

## Acciones Detectadas

- `fnjs_ver_equipaje`

## Capacidades Relacionadas

- `inventario.inventario_css_inline.gestionar`
- `inventario.inventario_ctr.gestionar`

## Endpoints Relacionados

- `/src/inventario/inventario_css_inline_data`
- `/src/inventario/inventario_ctr`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
