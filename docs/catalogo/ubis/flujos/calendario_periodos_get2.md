---
id: "ubis.calendario_periodos_get2.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Calendario Periodos Get2"
capacidad: "ubis.calendario_periodos_get2.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.calendario_periodos_get2"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_get2_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Get2

## Objetivo De Usuario

Lista los periodos de una casa en un año con detección de solapes.

## Punto De Entrada

Menú Legacy: adl > Nuevo Calendario > Definir periodos. Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_get2`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_ubi`
- `post.year`

Acciones JavaScript:
- `fnjs_modificar`

## Endpoints Del Flujo

- `/src/ubis/calendario_periodos_get2_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos
