---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Traslado — lista y guardar"
pantalla: "inventario.pantalla.traslado_doc_lista"
preguntas: ["Que se puede hacer en Traslado — lista y guardar?", "Que campos tiene Traslado — lista y guardar?", "Que acciones hay en Traslado — lista y guardar?"]
capacidades: ["inventario.lista_docs_de_ctr.gestionar"]
endpoints: ["/src/inventario/lista_docs_de_ctr"]
source: "docs/catalogo/inventario/pantallas/traslado_doc_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Traslado — lista y guardar

## Resumen

Lista docs del centro/lugar y ejecuta `traslado_doc_guardar`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_lugar`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_selectAll`

## Capacidades Relacionadas

- `inventario.lista_docs_de_ctr.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_de_ctr`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
