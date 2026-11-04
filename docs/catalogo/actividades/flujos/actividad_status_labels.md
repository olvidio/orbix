---
id: "actividades.actividad_status_labels.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver el estado de una actividad"
capacidad: "actividades.actividad_status_labels.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_status_labels_datos"]
estado_revision: "revisado"
---

# Flujo - Etiquetas de estado actividad

Mapa id → etiqueta legible de estados de actividad para el desplegable de la ficha.

## Objetivo De Usuario

Mostrar los nombres de estado correctos (proyecto, publicada, terminada…) según la sección y los permisos.

## Punto De Entrada

Ficha de actividad (menú **actividades > buscar actividad** y abrir una).

## Escenarios Inferidos

### Ver el estado de una actividad

Pasos propuestos:
1. Abrir la ficha de la actividad o el planning.
2. El desplegable de estado muestra los nombres que corresponden a su sección (sf/sv) y a los permisos del usuario.

Endpoints asociados:
- `/src/actividades/actividad_status_labels_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_status_labels_datos`

## Ruta de menú

sin entrada propia (paso interno de ficha/planning).
