---
id: "ubis.calendario_periodos_get.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Calendario Periodos Get"
capacidad: "ubis.calendario_periodos_get.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.calendario_periodos_get"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_get_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Get

## Objetivo De Usuario

Devuelve todos los periodos de calendario de una casa ordenados por fecha inicio.

## Punto De Entrada

Menú Legacy: adl > Nuevo Calendario > Definir periodos. Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_get`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_grabar`

## Endpoints Del Flujo

- `/src/ubis/calendario_periodos_get_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos
