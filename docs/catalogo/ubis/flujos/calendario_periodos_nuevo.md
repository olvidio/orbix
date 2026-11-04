---
id: "ubis.calendario_periodos_nuevo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Crear un periodo nuevo"
capacidad: "ubis.calendario_periodos_nuevo.gestionar"
pantallas_principales: ["ubis.pantalla.calendario_periodos_nuevo"]
fragmentos: ["ubis.pantalla.calendario_periodos_nuevo"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_nuevo_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Nuevo

## Objetivo De Usuario

Abrir el formulario de alta de un periodo ya relleno con la fecha siguiente al último.

## Punto De Entrada

Menú **adl > Nuevo Calendario > Definir periodos** (Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_nuevo`

## Escenarios Inferidos

### Crear un periodo nuevo

Pasos propuestos:
1. En **Definir periodos**, elegir la casa y el año y pulsar el botón de nuevo periodo.
2. El formulario propone como inicio el día siguiente al último periodo y su misma sección (sf/sv).
3. Ajustar las fechas y guardar.

Endpoints asociados:
- `/src/ubis/calendario_periodos_nuevo_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.f_fin`
- `form.f_ini`
- `form.sfsv`
- `post.id_ubi`
- `post.year`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/calendario_periodos_nuevo_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos
