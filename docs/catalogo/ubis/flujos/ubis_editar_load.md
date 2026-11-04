---
id: "ubis.ubis_editar_load.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Abrir la ficha de un centro o casa"
capacidad: "ubis.ubis_editar_load.gestionar"
pantallas_principales: ["ubis.pantalla.ubis_editar"]
fragmentos: ["ubis.pantalla.ubis_editar"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_load_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Editar Load

## Objetivo De Usuario

Cargar la ficha completa de un centro o casa para consultarla, modificarla o darla de alta.

## Punto De Entrada

Abrir un resultado de **buscar** o pulsar **Centro** / **Casa** para uno nuevo.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_editar`

## Escenarios Inferidos

### Abrir la ficha de un centro o casa

Pasos propuestos:
1. Buscar el centro o casa y pulsar su nombre.
2. La ficha se abre con sus datos: nombre, tipo, región o delegación, plazas, observaciones y, aparte, dirección y telecomunicaciones.
3. Para modificar, cambiar los campos y pulsar **guardar cambios**.

Endpoints asociados:
- `/src/ubis/ubis_editar_load_data`

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
