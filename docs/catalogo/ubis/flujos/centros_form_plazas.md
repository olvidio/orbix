---
id: "ubis.centros_form_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Cambiar plazas y sede"
capacidad: "ubis.centros_form_plazas.gestionar"
pantallas_principales: ["ubis.pantalla.centros_form_plazas"]
fragmentos: ["ubis.pantalla.centros_form_plazas"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_plazas"]
estado_revision: "revisado"
---

# Flujo - Centros Form Plazas

## Objetivo De Usuario

Cambiar las plazas, las habitaciones individuales y la marca de sede de un centro.

## Punto De Entrada

Menú **scdl > direcciones > modificar centros** (Pills2: CASAS Y CTR > direcciones > modificar centros).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_plazas`

## Escenarios Inferidos

### Cambiar plazas y sede

Pasos propuestos:
1. En **modificar centros > sede, plazas**, pulsar el centro.
2. Rellenar **plazas**, **habitaciones individuales** y marcar si es sede.
3. Pulsar **guardar cambios**.

Endpoints asociados:
- `/src/ubis/centros_form_plazas`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.num_habit_indiv`
- `form.plazas`
- `get.id_ubi`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/centros_form_plazas`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
