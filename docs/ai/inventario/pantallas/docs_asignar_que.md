---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Asignar documentos — selector"
pantalla: "inventario.pantalla.docs_asignar_que"
preguntas: ["Que se puede hacer en Asignar documentos — selector?", "Que campos tiene Asignar documentos — selector?", "Que acciones hay en Asignar documentos — selector?"]
capacidades: ["inventario.lista_tipo_doc.gestionar"]
endpoints: ["/src/inventario/lista_tipo_doc"]
source: "docs/catalogo/inventario/pantallas/docs_asignar_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Asignar documentos — selector

## Resumen

Elige tipo doc y acceso a asignados/no asignados/centros/DLB.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_tipo_doc`
- `html.okay`
- `html.okay2`
- `html.okay3`
- `html.okay4`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_go`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `inventario.lista_tipo_doc.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_tipo_doc`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
