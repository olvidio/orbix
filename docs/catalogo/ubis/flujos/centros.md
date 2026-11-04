---
id: "ubis.centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Guardar los datos de un centro"
capacidad: "ubis.centros.gestionar"
pantallas_principales: ["ubis.pantalla.centros_que"]
fragmentos: ["ubis.pantalla.centros_form_labor", "ubis.pantalla.centros_form_num", "ubis.pantalla.centros_form_plazas"]
acciones: ["crear_actualizar"]
endpoints: ["/src/ubis/centros_update"]
estado_revision: "revisado"
---

# Flujo - Centros

## Objetivo De Usuario

Guardar los cambios de un centro de la dl: labor, números o plazas.

## Punto De Entrada

Menú **scdl > direcciones > modificar centros**.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_labor`
- `ubis.pantalla.centros_form_num`
- `ubis.pantalla.centros_form_plazas`

## Escenarios Inferidos

### Guardar los datos de un centro

Pasos propuestos:
1. Elegir **labor**, **pi, cartas, nº buzón** o **sede, plazas** y pulsar **buscar**.
2. Abrir el centro, cambiar el bloque y pulsar **guardar cambios**.

Endpoints asociados:
- `/src/ubis/centros_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ubi`
- `form.n_buzon`
- `form.num_cartas`
- `form.num_habit_indiv`
- `form.num_pi`
- `form.plazas`
- `form.que`
- `form.tipo_ctr`
- `form.tipo_labor`
- `get.id_ubi`
- `html.buscar`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/ubis/centros_update`

## Errores Conocidos

- `Hay un error, no se ha guardado.`

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
