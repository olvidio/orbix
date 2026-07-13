---
id: "actividades.actividad_nivel_stgr_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Nivel STGR por defecto"
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

Al concretar tipo de actividad, el desplegable STGR se pre-rellena con el nivel habitual.

## Punto De Entrada

Ficha actividad: `fnjs_actualizar_nivel_stgr` tras cambio de tipo (`actividad_ver`).

## Endpoints Del Flujo

- `/src/actividades/actividad_nivel_stgr_default_datos`

## Ruta de menú

sin entrada propia (AJAX en ficha nueva/editar).
