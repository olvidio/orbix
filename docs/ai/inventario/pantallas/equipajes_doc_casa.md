---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Equipajes Doc Casa"
pantalla: "inventario.pantalla.equipajes_doc_casa"
preguntas: ["Que se puede hacer en Equipajes Doc Casa?", "Que campos tiene Equipajes Doc Casa?", "Que acciones hay en Equipajes Doc Casa?"]
capacidades: ["inventario.equipajes_doc_casa.gestionar", "inventario.equipajes_egm.gestionar"]
endpoints: ["/src/inventario/equipajes_doc_casa", "/src/inventario/equipajes_egm"]
source: "docs/catalogo/inventario/pantallas/equipajes_doc_casa.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Equipajes Doc Casa

## Resumen

Descripcion funcional pendiente de revisar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_equipaje`

## Acciones Detectadas

- `fnjs_eliminar_grupo`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_nuevo_grupo`
- `fnjs_ver_equipaje`

## Capacidades Relacionadas

- `inventario.equipajes_doc_casa.gestionar`
- `inventario.equipajes_egm.gestionar`

## Endpoints Relacionados

- `/src/inventario/equipajes_doc_casa`
- `/src/inventario/equipajes_egm`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
