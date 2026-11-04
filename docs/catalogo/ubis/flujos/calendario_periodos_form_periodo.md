---
id: "ubis.calendario_periodos_form_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Modificar un periodo"
capacidad: "ubis.calendario_periodos_form_periodo.gestionar"
pantallas_principales: ["ubis.pantalla.calendario_periodos_form_periodo"]
fragmentos: ["ubis.pantalla.calendario_periodos_form_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_form_periodo_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Form Periodo

## Objetivo De Usuario

Abrir el formulario de un periodo ya existente para modificarlo.

## Punto De Entrada

Menú **adl > Nuevo Calendario > Definir periodos** (Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_form_periodo`

## Escenarios Inferidos

### Modificar un periodo

Pasos propuestos:
1. En la lista de periodos de la casa, pulsar el periodo.
2. Cambiar fechas o sección y guardar.

Endpoints asociados:
- `/src/ubis/calendario_periodos_form_periodo_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.f_fin`
- `form.f_ini`
- `form.sfsv`
- `post.id_item`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/calendario_periodos_form_periodo_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos
