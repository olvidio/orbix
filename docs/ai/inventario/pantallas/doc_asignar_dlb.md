---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Formulario asignar DLB"
pantalla: "inventario.pantalla.doc_asignar_dlb"
preguntas: ["Que se puede hacer en Formulario asignar DLB?", "Que campos tiene Formulario asignar DLB?", "Que acciones hay en Formulario asignar DLB?"]
capacidades: ["inventario.doc_asignar_dlb.gestionar", "inventario.lista_docs_asignar_dlb.gestionar"]
endpoints: ["/src/inventario/doc_asignar_dlb_guardar", "/src/inventario/lista_docs_asignar_dlb"]
source: "docs/catalogo/inventario/pantallas/doc_asignar_dlb.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Formulario asignar DLB

## Resumen

Asignación DLB; guarda con `doc_asignar_dlb_guardar`.

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

- `inventario.doc_asignar_dlb.gestionar`
- `inventario.lista_docs_asignar_dlb.gestionar`

## Endpoints Relacionados

- `/src/inventario/doc_asignar_dlb_guardar`
- `/src/inventario/lista_docs_asignar_dlb`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
