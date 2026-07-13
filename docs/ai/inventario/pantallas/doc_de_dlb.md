---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Inventario DLB/casa"
pantalla: "inventario.pantalla.doc_de_dlb"
preguntas: ["Que se puede hacer en Inventario DLB/casa?", "Que campos tiene Inventario DLB/casa?", "Que acciones hay en Inventario DLB/casa?"]
capacidades: ["inventario.lista_docs_de_dlb.gestionar"]
endpoints: ["/src/inventario/lista_docs_de_dlb"]
source: "docs/catalogo/inventario/pantallas/doc_de_dlb.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Inventario DLB/casa

## Resumen

Consulta documentos DLB por tipo; asignar, modificar e imprimir.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

- `fnjs_go`
- `fnjs_selectAll`

## Capacidades Relacionadas

- `inventario.lista_docs_de_dlb.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_de_dlb`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
