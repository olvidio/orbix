---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Ubis Tabla"
pantalla: "ubis.pantalla.ubis_tabla"
preguntas: ["Que se puede hacer en Ubis Tabla?", "Que campos tiene Ubis Tabla?", "Que acciones hay en Ubis Tabla?"]
capacidades: ["ubis.ubis_tabla.gestionar"]
endpoints: ["/src/ubis/ubis_tabla_data"]
source: "docs/catalogo/ubis/pantallas/ubis_tabla.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ubis Tabla

## Resumen

Tabla de resultados de búsqueda de ubis con navegación y acciones sobre selección.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `html.b_mas`
- `post.stack`

## Acciones Detectadas

- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`

## Capacidades Relacionadas

- `ubis.ubis_tabla.gestionar`

## Endpoints Relacionados

- `/src/ubis/ubis_tabla_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
