---
id: "ubis.centros_form_labor.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Cambiar el tipo de labor"
capacidad: "ubis.centros_form_labor.gestionar"
pantallas_principales: ["ubis.pantalla.centros_form_labor"]
fragmentos: ["ubis.pantalla.centros_form_labor"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_labor"]
estado_revision: "revisado"
---

# Flujo - Centros Form Labor

## Objetivo De Usuario

Cambiar el tipo de labor de un centro.

## Punto De Entrada

Menú **scdl > direcciones > modificar centros** (Pills2: CASAS Y CTR > direcciones > modificar centros).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_labor`

## Escenarios Inferidos

### Cambiar el tipo de labor

Pasos propuestos:
1. En **modificar centros > labor**, pulsar el centro.
2. Elegir el **tipo de labor** y pulsar **guardar cambios**.

Endpoints asociados:
- `/src/ubis/centros_form_labor`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.tipo_ctr`
- `form.tipo_labor`
- `get.id_ubi`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/centros_form_labor`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
