---
id: "ubis.centros_form_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros Form Plazas"
capacidad: "ubis.centros_form_plazas.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.centros_form_plazas"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_plazas"]
estado_revision: "revisado"
---

# Flujo - Centros Form Plazas

## Objetivo De Usuario

Carga datos del formulario modal de plazas y sede de un centro DL.

## Punto De Entrada

Menú Legacy: scdl > direcciones > modificar centros. Pills2: scdl > direcciones > modificar centros.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_plazas`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
