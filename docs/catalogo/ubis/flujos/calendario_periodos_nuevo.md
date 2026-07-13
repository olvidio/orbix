---
id: "ubis.calendario_periodos_nuevo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Calendario Periodos Nuevo"
capacidad: "ubis.calendario_periodos_nuevo.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.calendario_periodos_nuevo"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_nuevo_data"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos Nuevo

## Objetivo De Usuario

Precarga el formulario de alta de periodo con fecha siguiente y sfsv del último periodo del año.

## Punto De Entrada

Menú Legacy: adl > Nuevo Calendario > Definir periodos. Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_nuevo`

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
