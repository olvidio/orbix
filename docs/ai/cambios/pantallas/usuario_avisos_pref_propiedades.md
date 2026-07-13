---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cambios"
titulo: "Tabla de propiedades"
pantalla: "cambios.pantalla.usuario_avisos_pref_propiedades"
preguntas: ["Que se puede hacer en Tabla de propiedades?", "Que campos tiene Tabla de propiedades?", "Que acciones hay en Tabla de propiedades?"]
capacidades: ["cambios.cambio_usuario_objeto_pref_propiedades.gestionar"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
source: "docs/catalogo/cambios/pantallas/usuario_avisos_pref_propiedades.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tabla de propiedades

## Resumen

Fragmento AJAX con la tabla de propiedades vigilables del objeto seleccionado (checkboxes y enlace a configurar condición).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.id_item_usuario_objeto_prop`
- `html.salida`
- `post.id_item_usuario_objeto`
- `post.objeto`

## Acciones Detectadas

- `fnjs_modificar`
- `fnjs_selectAll`

## Capacidades Relacionadas

- `cambios.cambio_usuario_objeto_pref_propiedades.gestionar`

## Endpoints Relacionados

- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
