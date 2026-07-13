---
id: "planning.planning_persona_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Planning por persona (calendario)"
capacidad: "planning.planning_persona_ver.gestionar"
pantallas_principales: ["planning.pantalla.planning_persona_que", "planning.pantalla.planning_persona_select"]
fragmentos: ["planning.pantalla.planning_persona_ver"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_persona_ver_data"]
estado_revision: "revisado"
---

# Flujo - Planning por persona (calendario)

Calendario de actividades de las personas seleccionadas en el listado.

## Objetivo De Usuario

Visualizar y exportar el planning individual o múltiple en el periodo elegido.

## Punto De Entrada

- Desde `planning_persona_select` → AJAX `planning_persona_ver`.

## Escenarios

### Ver calendario

1. Seleccionar persona(s) en el listado (`sel` o `sSeleccionados`).
2. `planning_persona_ver_data` carga actividades en vista plana.
3. Exportar o consultar leyenda.

## Endpoints Del Flujo

- `/src/planning/planning_persona_ver_data`

## Errores Conocidos

- `Faltan fechas de periodo`

## Ruta de menú

sin entrada de menú en el índice (subflujo del planning por persona)
