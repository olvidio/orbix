---
id: "ubis.calendario_periodos_get2.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Ver los periodos de un año"
capacidad: "ubis.calendario_periodos_get2.gestionar"
pantallas_principales: ["ubis.pantalla.calendario_periodos_get2"]
fragmentos: ["ubis.pantalla.calendario_periodos_get2"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_get2_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Get2

## Objetivo De Usuario

Listar los periodos de una casa en un año concreto y detectar solapes.

## Punto De Entrada

Menú **adl > Nuevo Calendario > Definir periodos** (Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_get2`

## Escenarios Inferidos

### Ver los periodos de un año

Pasos propuestos:
1. Entrar en **Definir periodos**, elegir la casa y el año y pulsar **buscar**.
2. Aparecen los periodos de ese año; los que se pisan quedan señalados.

Endpoints asociados:
- `/src/ubis/calendario_periodos_get2_data`

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
