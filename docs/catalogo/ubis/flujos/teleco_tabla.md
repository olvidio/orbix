---
id: "ubis.teleco_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Consultar las telecomunicaciones"
capacidad: "ubis.teleco_tabla.gestionar"
pantallas_principales: ["ubis.pantalla.teleco_tabla"]
fragmentos: ["ubis.pantalla.teleco_tabla"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/teleco_tabla"]
estado_revision: "revisado"
---

# Flujo - Teleco Tabla

## Objetivo De Usuario

Ver las telecomunicaciones (teléfonos, fax, e-mails, web) de un centro o casa.

## Punto De Entrada

Menú **scdl > direcciones > buscar**: buscar, pulsar el nombre del centro o casa en la tabla y usar «Editar: **centro** / **dirección** / **telecos**» de la ficha.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_tabla`

## Escenarios Inferidos

### Consultar las telecomunicaciones

Pasos propuestos:
1. En la ficha pulsar **telecos** en «Editar:».
2. Se muestra «Telecomunicaciones de un centro o casa» con Id_ubi, nombre del teleco (hoy sale su número de tipo), descripción, número o siglas y observaciones.
3. Los botones son **modificar** y **eliminar** (sobre las filas marcadas) y **nuevo**.

Endpoints asociados:
- `/src/ubis/teleco_tabla`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.mod`
- `form.sel`
- `html.btn_new`
- `html.mod`
- `html.refresh`
- `post.id_ubi`
- `post.obj_pau`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/ubis/teleco_tabla`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
