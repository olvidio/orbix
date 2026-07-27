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

1. Abrir entrada de menú (define `obj_pau`, `na`; `es_sacd` del menú se ignora en código).
2. Rellenar criterios opcionales y periodo en `planning_persona_que`.
3. `planning_persona_select_data` elige repositorio vía `obj_pau` (`getSafe` cae a `PersonaDl` si inválido); con `PersonaEx`, `na` → `id_tabla=p{na}`.
4. Seleccionar fila(s) (`sel[]` → `sSeleccionados`) y pulsar ver planning → `planning_persona_ver`.

### Acciones sobre fila

- Vista tabla / imprimir (`modelo=2`) / ver actividades (dossier 1301y1302) / ficha persona.

## Endpoints Del Flujo

- `/src/planning/planning_persona_select_data`

## Ruta de menú

| Params | Legacy | Pills2 |
|--------|--------|--------|
| `obj_pau=PersonaDl` | `… > planning > persona r/dl` · `scdl > persona dl` | `ACTIVIDADES > … > Plannig por personas` |
| `PersonaEx&na=a|n` | `scdl > planning > agd/num de paso` | igual |
| `PersonaSacd` | `dre > planning > sacd r/dl` | — |
| `PersonaSacd&es_sacd=1` | — | `ACTIVIDADES > … > Plannig por personas sacd` |
