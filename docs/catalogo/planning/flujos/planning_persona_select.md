---
id: "planning.planning_persona_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Planning por persona (listado)"
capacidad: "planning.planning_persona_select.gestionar"
pantallas_principales: ["planning.pantalla.planning_persona_que"]
fragmentos: ["planning.pantalla.planning_persona_select"]
acciones: ["buscar", "seleccionar", "ver_planning"]
endpoints: ["/src/planning/planning_persona_select_data"]
estado_revision: "revisado"
---

# Flujo - Planning por persona (listado)

Búsqueda de personas y selección para ver su planning.

## Objetivo De Usuario

Encontrar personas del colectivo del menú y abrir su calendario de actividades.

## Punto De Entrada

- `planning_persona_que.php` → `planning_persona_select.php`.

## Escenarios

### Buscar y listar

1. Abrir entrada de menú (define `obj_pau`, `na`).
2. Rellenar criterios opcionales y periodo en `planning_persona_que`.
3. `planning_persona_select_data` devuelve tabla de personas.
4. Seleccionar fila(s) y pulsar ver planning → `planning_persona_ver`.

### Acciones sobre fila

- Ficha persona, dossier, imprimir planning de una persona.

## Endpoints Del Flujo

- `/src/planning/planning_persona_select_data`

## Ruta de menú

- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Plannig por personas` (u otras variantes sacd/de paso)
- **Legacy:** `dre > planning > persona r/dl` (según `obj_pau`/`na`)
