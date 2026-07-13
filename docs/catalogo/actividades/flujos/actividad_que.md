---
id: "actividades.actividad_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Selector tipo en buscar actividad"
capacidad: "actividades.actividad_que.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_que"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_que_datos"]
estado_revision: "revisado"
---

# Flujo - Selector tipo en buscar actividad

HTML inicial de la cascada sf/sv → asistentes → actividad → tipo en pantallas de búsqueda y planning.

## Objetivo De Usuario

Al cargar `actividad_que` o el bloque tipo del planning, ver desplegables coherentes con
permisos y parámetros (`sasistentes`, `sactividad`, `ssfsv`).

## Punto De Entrada

- `actividad_que.php` (PostRequest al renderizar).
- `planning_casa_nueva` / `planning_casa_modificar` (bloque tipo).

## Escenarios

### Obtener Datos

1. Controller envía contexto (colectivo, dl, modo).
2. `actividad_que_datos` devuelve HTML del selector de tipo.
3. Twig/phtml inserta el bloque en el formulario.

## Endpoints Del Flujo

- `/src/actividades/actividad_que_datos`

## Ruta de menú

Herencia de `actividad_que` (buscar/importar/publicar activ y entradas por colectivo).
