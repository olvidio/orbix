---
id: "ubis.home_ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Ver el resumen de un centro o casa"
capacidad: "ubis.home_ubis.gestionar"
pantallas_principales: ["ubis.pantalla.home_ubis"]
fragmentos: ["ubis.pantalla.home_ubis"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/home_ubis_data"]
estado_revision: "revisado"
---

# Flujo - Home Ubis

## Objetivo De Usuario

Mostrar la ficha resumen de un centro o casa con su dirección y telecomunicaciones.

## Punto De Entrada

Resultado de **buscar** o enlace desde otras fichas.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.home_ubis`

## Escenarios Inferidos

### Ver el resumen de un centro o casa

Pasos propuestos:
1. Abrir el centro o casa.
2. Aparecen los apartados **dirección** y **telecos**, cada uno con su botón **editar**.

Endpoints asociados:
- `/src/ubis/home_ubis_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.bloque`
- `post.id_ubi`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_left_side_show`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/home_ubis_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
