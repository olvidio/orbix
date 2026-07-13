---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Centros sin documento"
pantalla: "inventario.pantalla.doc_no_asignado"
preguntas: ["Que se puede hacer en Centros sin documento?", "Que campos tiene Centros sin documento?", "Que acciones hay en Centros sin documento?"]
capacidades: ["inventario.lista_docs_no_asignados_por_tipo.gestionar"]
endpoints: ["/src/inventario/lista_docs_no_asignados_por_tipo"]
source: "docs/catalogo/inventario/pantallas/doc_no_asignado.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Centros sin documento

## Resumen

Centros pendientes de recibir el tipo doc.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_tipo_doc`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `inventario.lista_docs_no_asignados_por_tipo.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_no_asignados_por_tipo`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
