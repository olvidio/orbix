---
id: "planning.planning_ctr_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Planning por centro (calendario)"
capacidad: "planning.planning_ctr_select.gestionar"
pantallas_principales: ["planning.pantalla.planning_ctr_que"]
fragmentos: ["planning.pantalla.planning_ctr_select"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_ctr_select_data"]
estado_revision: "revisado"
---

# Flujo - Planning por centro (calendario)

Calendario de personas y actividades por centro tras enviar filtros en `planning_ctr_que`.

## Objetivo De Usuario

Ver el planning de un centro o de todos los centros (por colectivo n/agd/s) en un periodo.

## Punto De Entrada

- `planning_ctr_que.php` → AJAX `planning_ctr_select`.

## Escenarios

### Ver calendario

1. Elegir centro o checkboxes de colectivo (`todos_n`/`todos_agd`/`todos_s`; último gana; default `n` si ctr vacío), periodo y filtro sacd (`sacd===''` excluye; `0` no).
2. `planning_ctr_select_data` resuelve personas (`PersonaDl`) y actividades agrupadas; `obj_pau` del form no afecta al API.
3. Revisar avisos (`msg_txt`) si algún ctr no tiene personas (`No encuentro este ctr` / `No encuentro personas para %s`).
4. Exportar (`modelo=2`) o consultar leyenda.

## Endpoints Del Flujo

- `/src/planning/planning_ctr_select_data`

## Errores Conocidos

- `Faltan fechas de periodo`
- `No encuentro este ctr`
- `No encuentro personas para %s`

## Ruta de menú

- **Legacy:** `dre/Calendario/… > planning > por centro` · `vsr/scdl > planning > por ctr`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Planning por ctr`
