---
id: "ubis.centros_get_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros Get Plazas"
capacidad: "ubis.centros_get_plazas.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.centros_get_plazas"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_get_plazas"]
estado_revision: "revisado"
---

# Flujo - Centros Get Plazas

## Objetivo De Usuario

Lista centros DL activos con plazas, habitaciones individuales y flag sede.

## Punto De Entrada

Menú Legacy: scdl > direcciones > modificar centros. Pills2: scdl > direcciones > modificar centros.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_get_plazas`

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
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/centros_get_plazas`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
