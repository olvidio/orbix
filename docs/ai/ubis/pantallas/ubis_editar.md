---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Ubis Editar"
pantalla: "ubis.pantalla.ubis_editar"
preguntas: ["Que se puede hacer en Ubis Editar?", "Que campos tiene Ubis Editar?", "Que acciones hay en Ubis Editar?"]
capacidades: ["ubis.ubis_editar.gestionar", "ubis.ubis_editar_load.gestionar"]
endpoints: ["/src/ubis/ubis_editar_data", "/src/ubis/ubis_editar_load_data"]
source: "docs/catalogo/ubis/pantallas/ubis_editar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ubis Editar

## Resumen

Formulario de edición o alta de ficha de centro o casa dentro de la ficha ubi.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.active`
- `html.cdc`
- `html.n_buzon`
- `html.nombre_ubi`
- `html.num_cartas`
- `html.num_cartas_mensuales`
- `html.num_habit_indiv`
- `html.num_pi`
- `html.num_sacd`
- `html.observ`
- `html.plazas`
- `html.plazas_min`
- `html.que`
- `html.sf`
- `html.status`
- `html.sv`
- `html.tipo_ubi`
- `post.dl`
- `post.id_ubi`
- `post.nombre_ubi`
- `post.nuevo`
- `post.obj_pau`
- `post.region`
- `post.tipo_ubi`

## Acciones Detectadas

- `fnjs_eliminar`
- `fnjs_guardar`

## Capacidades Relacionadas

- `ubis.ubis_editar.gestionar`
- `ubis.ubis_editar_load.gestionar`

## Endpoints Relacionados

- `/src/ubis/ubis_editar_data`
- `/src/ubis/ubis_editar_load_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
