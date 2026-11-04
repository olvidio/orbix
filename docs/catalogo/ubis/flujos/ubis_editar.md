---
id: "ubis.ubis_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Rellenar la ficha de un centro o casa"
capacidad: "ubis.ubis_editar.gestionar"
pantallas_principales: ["ubis.pantalla.ubis_editar"]
fragmentos: ["ubis.pantalla.ubis_editar"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Editar

## Objetivo De Usuario

Cambiar los datos básicos de un centro o casa: delegación, nombre, tipo, labor, observaciones y números.

## Punto De Entrada

Menú **scdl > direcciones > buscar**: buscar, pulsar el nombre del centro o casa en la tabla y usar «Editar: **centro** / **dirección** / **telecos**» de la ficha.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_editar`

## Escenarios Inferidos

### Rellenar la ficha de un centro o casa

Pasos propuestos:
1. Abrir la ficha del centro (aparece su nombre, la dl, la dirección, los teléfonos, fax y e-mails y la «Relación de dossiers»).
2. Pulsar **centro** en «Editar:».
3. Rellenar **Región o delegación**, **En uso**, **Región**, **Nombre del centro**, **¿puede ser cdc?**, **Tipo de centro** (por ejemplo «colegio oc»), **Tipo de labor** (sr, n, agd, sg, club, bachilleres, univ, jóvenes, mayores, sss+), **Observaciones** y **Centro padre**.
4. Más abajo están **Nº ejemplares pi**, **Nº cartas**, **Nº cartas mensuales**, **Nº de buzón**, **Habitaciones individuales** y **Plazas**.
5. Pulsar **Guardar Cambios**. El botón **Eliminar** borra el centro (pide confirmación).
6. Al cambiar un desplegable, el siguiente se rellena con las opciones válidas.

Endpoints asociados:
- `/src/ubis/ubis_editar_data`

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

- `/src/ubis/ubis_editar_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
