---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Formulario asignar a centros"
pantalla: "inventario.pantalla.doc_asignar_ctr"
preguntas: ["Que se puede hacer en Formulario asignar a centros?", "Que campos tiene Formulario asignar a centros?", "Que acciones hay en Formulario asignar a centros?"]
capacidades: ["inventario.doc_asignar_ctr.gestionar", "inventario.lista_docs_asignar_ctr.gestionar"]
endpoints: ["/src/inventario/doc_asignar_ctr_guardar", "/src/inventario/lista_docs_asignar_ctr"]
source: "docs/catalogo/inventario/pantallas/doc_asignar_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Formulario asignar a centros

## Resumen

Formulario numérico por centro; guarda con `doc_asignar_ctr_guardar`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.f_asignado`
- `html.f_recibido`
- `html.okay`
- `post.id_tipo_doc`
- `post.sel`

## Acciones Detectadas

- `fnjs_guardar`

## Capacidades Relacionadas

- `inventario.doc_asignar_ctr.gestionar`
- `inventario.lista_docs_asignar_ctr.gestionar`

## Endpoints Relacionados

- `/src/inventario/doc_asignar_ctr_guardar`
- `/src/inventario/lista_docs_asignar_ctr`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
