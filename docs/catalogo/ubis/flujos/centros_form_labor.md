---
id: "ubis.centros_form_labor.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros Form Labor"
capacidad: "ubis.centros_form_labor.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.centros_form_labor"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_labor"]
estado_revision: "revisado"
---

# Flujo - Centros Form Labor

## Objetivo De Usuario

Carga datos del formulario modal de tipo de labor de un centro DL.

## Punto De Entrada

Menú Legacy: scdl > direcciones > modificar centros. Pills2: scdl > direcciones > modificar centros.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_labor`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
