---
id: "ubis.ubis_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Trabajar con los resultados de la búsqueda"
capacidad: "ubis.ubis_tabla.gestionar"
pantallas_principales: ["ubis.pantalla.ubis_tabla"]
fragmentos: ["ubis.pantalla.ubis_tabla"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_tabla_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Tabla

## Objetivo De Usuario

Mostrar la tabla de centros y casas encontrados y permitir abrir, crear, eliminar o trasladar.

## Punto De Entrada

Resultado de la búsqueda de **buscar** (scdl > direcciones > buscar).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_tabla`

## Escenarios Inferidos

### Trabajar con los resultados de la búsqueda

Pasos propuestos:
1. Tras buscar, aparece «Tabla de toda las casas y centros».
2. Pulsar un nombre para abrir su ficha.
3. Para crear uno nuevo, usar el botón **Centro** o **Casa** (solo aparecen si se buscó con un tipo y una localización distintos de «todos»; con «todos» la pantalla solo ofrece **buscar otras opciones** y avisa «OJO!: para crear un centro/casa debe especificar el tipo de centro/casa»).
4. Para quitar uno, marcarlo y pulsar eliminar; confirmar «¿Está seguro que desea borrar este ubi?».

Endpoints asociados:
- `/src/ubis/ubis_tabla_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `html.b_mas`
- `post.stack`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/ubis_tabla_data`

## Errores Conocidos

- `debe poner algún criterio de búsqueda`

## Ruta de menú

- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar
