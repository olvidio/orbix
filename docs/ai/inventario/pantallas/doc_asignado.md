---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Documentos ya asignados"
pantalla: "inventario.pantalla.doc_asignado"
preguntas: ["Que se puede hacer en Documentos ya asignados?", "Que campos tiene Documentos ya asignados?", "Que acciones hay en Documentos ya asignados?"]
capacidades: ["inventario.lista_docs_asignados_por_tipo.gestionar"]
endpoints: ["/src/inventario/lista_docs_asignados_por_tipo"]
source: "docs/catalogo/inventario/pantallas/doc_asignado.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Documentos ya asignados

## Resumen

Lista centros con el tipo doc asignado.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_tipo_doc`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `inventario.lista_docs_asignados_por_tipo.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_asignados_por_tipo`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
