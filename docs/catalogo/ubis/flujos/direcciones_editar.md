---
id: "ubis.direcciones_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Modificar la dirección"
capacidad: "ubis.direcciones_editar.gestionar"
pantallas_principales: ["ubis.pantalla.direcciones_editar"]
fragmentos: ["ubis.pantalla.direcciones_editar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_editar"]
estado_revision: "revisado"
---

# Flujo - Direcciones Editar

## Objetivo De Usuario

Modificar la dirección de un centro o casa y gestionar su plano.

## Punto De Entrada

Menú **scdl > direcciones > buscar**: buscar, pulsar el nombre del centro o casa en la tabla y usar «Editar: **centro** / **dirección** / **telecos**» de la ficha.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_editar`

## Escenarios Inferidos

### Modificar la dirección

Pasos propuestos:
1. En la ficha pulsar **dirección** en «Editar:».
2. Cambiar los campos que hagan falta (dirección, código postal, población, provincia, país, latitud y longitud, comentarios).
3. Para el plano: **ver plano**, **adjuntar plano** o **eliminar plano**.
4. Pulsar **Guardar Cambios**.

Endpoints asociados:
- `/src/ubis/direcciones_editar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.a_p`
- `form.act`
- `form.c_p`
- `form.direccion`
- `form.f_direccion`
- `form.id_direccion`
- `form.id_ubi`
- `form.latitud`
- `form.longitud`
- `form.nom_sede`
- `form.obj_dir`
- `form.observ`
- `form.pais`
- `form.poblacion`
- `form.provincia`
- `form.que`
- `html.a_p`
- `html.c_p`
- `html.cp_dcha`
- `html.direccion`
- `html.f_direccion`
- `html.latitud`
- `html.longitud`
- `html.nom_sede`
- `html.observ`
- `html.pais`
- `html.poblacion`
- `html.principal`
- `html.propietario`
- `html.provincia`
- `html.que`
- `post.id_direccion`
- `post.id_ubi`
- `post.idx`
- `post.inc`
- `post.mod`
- `post.obj_dir`
- `post.refresh`

Acciones JavaScript:
- `fnjs_add_dir`
- `fnjs_adjuntar`
- `fnjs_asignar_dir`
- `fnjs_eliminar`
- `fnjs_guardar_dir`
- `fnjs_otro`
- `fnjs_quitar_dir`
- `fnjs_update_div`
- `fnjs_ver_dir`
- `fnjs_ver_documento`

## Endpoints Del Flujo

- `/src/ubis/direcciones_editar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
