---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Añadir doc a maleta"
pantalla: "inventario.pantalla.equipajes_form_add"
preguntas: ["Que se puede hacer en Añadir doc a maleta?", "Que campos tiene Añadir doc a maleta?", "Que acciones hay en Añadir doc a maleta?"]
capacidades: ["inventario.lista_tipo_doc.gestionar"]
endpoints: ["/src/inventario/lista_tipo_doc"]
source: "docs/catalogo/inventario/pantallas/equipajes_form_add.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Añadir doc a maleta

## Resumen

Formulario add doc → `equipajes_add_doc`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_tipo_doc`
- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`

## Acciones Detectadas

- `fnjs_add_doc`
- `fnjs_cerrar`
- `fnjs_docs_libres`

## Capacidades Relacionadas

- `inventario.lista_tipo_doc.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_tipo_doc`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
