---
id: "ubis.ubis_editar_load.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Ubis Editar Load"
capacidad: "ubis.ubis_editar_load.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_editar"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_load_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Editar Load

## Objetivo De Usuario

Carga la ficha completa de un ubi para edición o alta, normalizando obj_pau de delegación.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_editar`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
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

Acciones JavaScript:
- `fnjs_eliminar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/ubis_editar_load_data`

## Errores Conocidos

- `falta definir obj_pau`
- `No se encuentra ubi id %s`
- `tipo de entidad inesperado para centro dl`
- `tipo de entidad inesperado para centro ex`
- `tipo de entidad inesperado para casa`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
