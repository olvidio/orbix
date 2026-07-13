---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Docs libres"
pantalla: "inventario.pantalla.equipajes_docs_libres"
preguntas: ["Que se puede hacer en Docs libres?", "Que campos tiene Docs libres?", "Que acciones hay en Docs libres?"]
capacidades: ["inventario.lista_docs_libres.gestionar"]
endpoints: ["/src/inventario/lista_docs_libres"]
source: "docs/catalogo/inventario/pantallas/equipajes_docs_libres.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Docs libres

## Resumen

Documentos disponibles para añadir a maleta.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.sel[]`
- `post.id_equipaje`
- `post.id_tipo_doc`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `inventario.lista_docs_libres.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_docs_libres`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
