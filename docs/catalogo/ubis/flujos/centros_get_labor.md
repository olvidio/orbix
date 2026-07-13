---
id: "ubis.centros_get_labor.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros Get Labor"
capacidad: "ubis.centros_get_labor.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.centros_get_labor"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_get_labor"]
estado_revision: "revisado"
---

# Flujo - Centros Get Labor

## Objetivo De Usuario

Lista todos los centros DL activos con su tipo de centro y tipo de labor.

## Punto De Entrada

Menú Legacy: scdl > direcciones > modificar centros. Pills2: scdl > direcciones > modificar centros.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_get_labor`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_modificar`

## Endpoints Del Flujo

- `/src/ubis/centros_get_labor`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
