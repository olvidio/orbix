---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Inventario por centros"
pantalla: "inventario.pantalla.doc_de_ctr"
preguntas: ["Que se puede hacer en Inventario por centros?", "Que campos tiene Inventario por centros?", "Que acciones hay en Inventario por centros?"]
capacidades: ["inventario.lista_de_ctr_con_docs.gestionar"]
endpoints: ["/src/inventario/lista_de_ctr_con_docs"]
source: "docs/catalogo/inventario/pantallas/doc_de_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Inventario por centros

## Resumen

Selección de tipo doc y centros; enlaces a asignar, modificar e imprimir inventario CTR.

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

- `inventario.lista_de_ctr_con_docs.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_de_ctr_con_docs`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
