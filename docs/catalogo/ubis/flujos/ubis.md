---
id: "ubis.ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Eliminar un centro o una casa"
capacidad: "ubis.ubis.gestionar"
pantallas_principales: ["ubis.pantalla.ubis_eliminar", "ubis.pantalla.ubis_lista", "ubis.pantalla.ubis_update"]
fragmentos: ["ubis.pantalla.ubis_eliminar", "ubis.pantalla.ubis_lista", "ubis.pantalla.ubis_update"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/ubis/ubis_eliminar", "/src/ubis/ubis_guardar", "/src/ubis/ubis_lista_data"]
estado_revision: "revisado"
---

# Flujo - Ubis

## Objetivo De Usuario

Eliminar un centro o una casa.

## Punto De Entrada

Resultados de **buscar**, o botón **eliminar** de la ficha.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_eliminar`
- `ubis.pantalla.ubis_lista`
- `ubis.pantalla.ubis_update`

## Escenarios Inferidos

### Eliminar un centro o una casa

Pasos propuestos:
1. Abrir la ficha, o marcar el centro o casa en la tabla de resultados.
2. Pulsar **eliminar** y confirmar «¿Está seguro que desea borrar este centro?» (o «esta casa»).

Endpoints asociados:
- `/src/ubis/ubis_eliminar`
- `/src/ubis/ubis_guardar`
- `/src/ubis/ubis_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_ubi`
- `post.nombre_ubi`
- `post.obj_pau`

Acciones JavaScript:
- `fnjs_buscar`

## Endpoints Del Flujo

- `/src/ubis/ubis_eliminar`
- `/src/ubis/ubis_guardar`
- `/src/ubis/ubis_lista_data`

## Errores Conocidos

- `no se encuentra el ubi a borrar`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
