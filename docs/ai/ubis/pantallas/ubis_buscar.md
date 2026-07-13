---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Ubis Buscar"
pantalla: "ubis.pantalla.ubis_buscar"
preguntas: ["Que se puede hacer en Ubis Buscar?", "Que campos tiene Ubis Buscar?", "Que acciones hay en Ubis Buscar?"]
capacidades: ["ubis.ubis_buscar.gestionar"]
endpoints: ["/src/ubis/ubis_buscar_data"]
source: "docs/catalogo/ubis/pantallas/ubis_buscar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ubis Buscar

## Resumen

Formulario de criterios de búsqueda de centros y casas que alimenta ubis_tabla.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.b_buscar`
- `html.b_mas`
- `html.cmb`
- `html.labor[]`
- `html.loc`
- `html.ok`
- `html.opcion`
- `html.select[]`
- `html.simple`
- `html.tipo`
- `post.loc`
- `post.simple`
- `post.tipo`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_update_div`
- `fnjs_ver_solo`

## Capacidades Relacionadas

- `ubis.ubis_buscar.gestionar`

## Endpoints Relacionados

- `/src/ubis/ubis_buscar_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
