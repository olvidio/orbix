---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Ver docs de lugar"
pantalla: "inventario.pantalla.equipajes_ver_docs"
preguntas: ["Que se puede hacer en Ver docs de lugar?", "Que campos tiene Ver docs de lugar?", "Que acciones hay en Ver docs de lugar?"]
capacidades: ["inventario.lista_docs_de_lugar.gestionar"]
endpoints: ["/src/inventario/lista_docs_de_lugar"]
source: "docs/catalogo/inventario/pantallas/equipajes_ver_docs.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ver docs de lugar

## Resumen

Lista documentos de un lugar en equipaje.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.nom_grupo`

## Acciones Detectadas

- `fnjs_update_grupo`

## Capacidades Relacionadas

- `inventario.lista_docs_de_lugar.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_de_lugar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
