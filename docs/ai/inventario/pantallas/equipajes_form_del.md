---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Quitar doc de maleta"
pantalla: "inventario.pantalla.equipajes_form_del"
preguntas: ["Que se puede hacer en Quitar doc de maleta?", "Que campos tiene Quitar doc de maleta?", "Que acciones hay en Quitar doc de maleta?"]
capacidades: ["inventario.lista_docs_de_egm.gestionar"]
endpoints: ["/src/inventario/lista_docs_de_egm"]
source: "docs/catalogo/inventario/pantallas/equipajes_form_del.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Quitar doc de maleta

## Resumen

Formulario del doc → `equipajes_del_doc`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_del_doc`

## Capacidades Relacionadas

- `inventario.lista_docs_de_egm.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_de_egm`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
