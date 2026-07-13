---
id: "actividades.actividad_status_labels.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Etiquetas de estado actividad"
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

Ver nombres de estado correctos según sf/sv y permisos al abrir ficha o planning.

## Punto De Entrada

Render de `actividad_ver` / planning (PostRequest en servidor al montar formulario).

## Endpoints Del Flujo

- `/src/actividades/actividad_status_labels_datos`

## Ruta de menú

sin entrada propia (paso interno de ficha/planning).
