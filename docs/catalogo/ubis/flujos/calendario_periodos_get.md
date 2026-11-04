---
id: "ubis.calendario_periodos_get.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Ver todos los periodos de una casa"
capacidad: "ubis.calendario_periodos_get.gestionar"
pantallas_principales: ["ubis.pantalla.calendario_periodos_get"]
fragmentos: ["ubis.pantalla.calendario_periodos_get"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_get_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Get

## Objetivo De Usuario

Listar todos los periodos de calendario de una casa ordenados por fecha de inicio.

## Punto De Entrada

Menú **adl > Nuevo Calendario > Definir periodos** (Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_get`

## Escenarios Inferidos

### Ver todos los periodos de una casa

Pasos propuestos:
1. Entrar en **Definir periodos**, elegir la casa y pulsar **buscar**.
2. Aparecen todos sus periodos por orden de fecha.

Endpoints asociados:
- `/src/ubis/calendario_periodos_get_data`

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
