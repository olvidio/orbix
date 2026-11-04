---
id: "ubis.centros_form_num.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Cambiar buzón, pi y cartas"
capacidad: "ubis.centros_form_num.gestionar"
pantallas_principales: ["ubis.pantalla.centros_form_num"]
fragmentos: ["ubis.pantalla.centros_form_num"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_num"]
estado_revision: "revisado"
---

# Flujo - Centros Form Num

## Objetivo De Usuario

Cambiar el nº de buzón, de ejemplares de pi y de cartas de un centro.

## Punto De Entrada

Menú **scdl > direcciones > modificar centros** (Pills2: CASAS Y CTR > direcciones > modificar centros).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_num`

## Escenarios Inferidos

### Cambiar buzón, pi y cartas

Pasos propuestos:
1. En **modificar centros > pi, cartas, nº buzón**, pulsar el centro.
2. Rellenar **nº de buzón**, **nº ejemplares pi** y **nº cartas mensuales**.
3. Pulsar **guardar cambios**.

Endpoints asociados:
- `/src/ubis/centros_form_num`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.n_buzon`
- `form.num_cartas`
- `form.num_pi`
- `get.id_ubi`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/centros_form_num`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
