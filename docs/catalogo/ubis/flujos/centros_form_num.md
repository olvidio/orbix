---
id: "ubis.centros_form_num.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros Form Num"
capacidad: "ubis.centros_form_num.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.centros_form_num"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_num"]
estado_revision: "revisado"
---

# Flujo - Centros Form Num

## Objetivo De Usuario

Carga datos del formulario modal de números (buzón, pi, cartas) de un centro DL.

## Punto De Entrada

Menú Legacy: scdl > direcciones > modificar centros. Pills2: scdl > direcciones > modificar centros.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_num`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
