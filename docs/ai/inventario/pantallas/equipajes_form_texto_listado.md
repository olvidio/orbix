---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Editar texto listado"
pantalla: "inventario.pantalla.equipajes_form_texto_listado"
preguntas: ["Que se puede hacer en Editar texto listado?", "Que campos tiene Editar texto listado?", "Que acciones hay en Editar texto listado?"]
capacidades: ["inventario.texto_de_egm.gestionar"]
endpoints: ["/src/inventario/texto_de_egm"]
source: "docs/catalogo/inventario/pantallas/equipajes_form_texto_listado.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Editar texto listado

## Resumen

Editor texto grupo EGM.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.texto`
- `html.texto`
- `post.id_equipaje`
- `post.loc`
- `post.texto`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar_listado`

## Capacidades Relacionadas

- `inventario.texto_de_egm.gestionar`

## Endpoints Relacionados

- `/src/inventario/texto_de_egm`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
