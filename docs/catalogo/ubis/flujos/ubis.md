---
id: "ubis.ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Ubis"
capacidad: "ubis.ubis.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_eliminar", "ubis.pantalla.ubis_lista", "ubis.pantalla.ubis_update"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/ubis/ubis_eliminar", "/src/ubis/ubis_guardar", "/src/ubis/ubis_lista_data"]
estado_revision: "revisado"
---

# Flujo - Ubis

## Objetivo De Usuario

Elimina un ubi (centro o casa) del repositorio correspondiente a obj_pau.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_eliminar`
- `ubis.pantalla.ubis_lista`
- `ubis.pantalla.ubis_update`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/ubis/ubis_eliminar`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
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
