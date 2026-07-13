---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "configuracion"
titulo: "Definir módulos"
pantalla: "configuracion.pantalla.modulos_select"
preguntas: ["Que se puede hacer en Definir módulos?", "Que campos tiene Definir módulos?", "Que acciones hay en Definir módulos?"]
capacidades: ["configuracion.modulos_select.gestionar"]
endpoints: ["/src/configuracion/modulos_select_data"]
source: "docs/catalogo/configuracion/pantallas/modulos_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Definir módulos

## Resumen

Listado de módulos del esquema Orbix: nombre, descripción, módulos requeridos y aplicaciones requeridas. Permite alta, modificación y baja desde botones de fila y enlace «añadir módulo».

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.mod`
- `html.refresh`
- `post.sel`
- `post.id_sel`
- `post.scroll_id`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Capacidades Relacionadas

- `configuracion.modulos_select.gestionar`

## Endpoints Relacionados

- `/src/configuracion/modulos_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
