---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Lista docs EGM/lugar"
pantalla: "inventario.pantalla.equipajes_lista_docs"
preguntas: ["Que se puede hacer en Lista docs EGM/lugar?", "Que campos tiene Lista docs EGM/lugar?", "Que acciones hay en Lista docs EGM/lugar?"]
capacidades: ["inventario.lista_docs_de_egm.gestionar", "inventario.lista_docs_de_lugar.gestionar"]
endpoints: ["/src/inventario/lista_docs_de_egm", "/src/inventario/lista_docs_de_lugar"]
source: "docs/catalogo/inventario/pantallas/equipajes_lista_docs.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Lista docs EGM/lugar

## Resumen

Docs de maleta o lugar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`
- `post.id_lugar`

## Acciones Detectadas

- `fnjs_eliminar_grupo`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`

## Capacidades Relacionadas

- `inventario.lista_docs_de_egm.gestionar`
- `inventario.lista_docs_de_lugar.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_de_egm`
- `/src/inventario/lista_docs_de_lugar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
