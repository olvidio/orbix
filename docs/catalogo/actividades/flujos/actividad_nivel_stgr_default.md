---
id: "actividades.actividad_nivel_stgr_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Elegir el tipo y el nivel STGR"
capacidad: "actividades.actividad_nivel_stgr_default.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_nivel_stgr_default_datos"]
estado_revision: "revisado"
---

# Flujo - Nivel STGR por defecto

Valor por defecto del campo nivel STGR al cambiar tipo o abrir ficha nueva (según tipo y dl).

## Objetivo De Usuario

Al elegir el tipo de actividad, rellenar solo el nivel STGR habitual.

## Punto De Entrada

Ficha de actividad (menú **actividades > buscar actividad** y abrir una).

## Escenarios Inferidos

### Elegir el tipo y el nivel STGR

Pasos propuestos:
1. En la ficha, elegir el tipo de actividad.
2. El desplegable de nivel STGR se pre-rellena con el nivel habitual para ese tipo; se puede cambiar.

Endpoints asociados:
- `/src/actividades/actividad_nivel_stgr_default_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_nivel_stgr_default_datos`

## Ruta de menú

sin entrada propia (AJAX en ficha nueva/editar).
