---
id: "cartaspresentacion.ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Ver los centros y sus cartas"
capacidad: "cartaspresentacion.ubis.gestionar"
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"]
acciones: ["listar"]
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
estado_revision: "revisado"
---

# Flujo - Listado de centros con estado de carta

Tabla interactiva de centros y su carta de presentación (sí/no).

## Objetivo De Usuario

Ver qué centros tienen carta de presentación y acceder a modificarla, ver la ficha o quitarla.

## Punto De Entrada

Resultado de **buscar** en la pantalla de modificar cartas de presentación.

## Escenarios Inferidos

### Ver los centros y sus cartas

Pasos propuestos:
1. Elegir el filtro (delegación o regiones y, en el primer caso, la población).
2. Pulsar **buscar**.
3. Aparece la tabla con las columnas director, centro, carta de presentación y dirección.

Endpoints asociados:
- `/src/cartaspresentacion/ubis_lista_data`

## Endpoints Del Flujo

- `/src/cartaspresentacion/ubis_lista_data`

## Ruta de menú

sin entrada de menú en el índice (fragmento de `cartas_presentacion` > modificar).
