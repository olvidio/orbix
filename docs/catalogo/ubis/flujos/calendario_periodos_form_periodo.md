---
id: "ubis.calendario_periodos_form_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Calendario Periodos Form Periodo"
capacidad: "ubis.calendario_periodos_form_periodo.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.calendario_periodos_form_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_form_periodo_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Form Periodo

## Objetivo De Usuario

Carga los campos del formulario de edición de un periodo de calendario existente.

## Punto De Entrada

Menú Legacy: adl > Nuevo Calendario > Definir periodos. Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_form_periodo`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
