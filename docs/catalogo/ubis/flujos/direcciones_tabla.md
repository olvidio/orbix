---
id: "ubis.direcciones_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Elegir una dirección de la tabla"
capacidad: "ubis.direcciones_tabla.gestionar"
pantallas_principales: ["ubis.pantalla.direcciones_tabla"]
fragmentos: ["ubis.pantalla.direcciones_tabla"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_tabla"]
estado_revision: "revisado"
---

# Flujo - Direcciones Tabla

## Objetivo De Usuario

Mostrar las direcciones encontradas para asignar una al centro o casa, o crear otra.

## Punto De Entrada

Tras buscar en **añadir dirección**.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_tabla`

## Escenarios Inferidos

### Elegir una dirección de la tabla

Pasos propuestos:
1. Revisar la «tabla de direcciones».
2. Marcar la dirección que corresponde y asignarla.
3. Si no está, pulsar **crear otra nueva**.

Endpoints asociados:
- `/src/ubis/direcciones_tabla`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.c_p`
- `post.ciudad`
- `post.id_ubi`
- `post.obj_dir`
- `post.pais`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/direcciones_tabla`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
