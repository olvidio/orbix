---
id: "planning.planning_casa_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Planning por casas (filtros)"
capacidad: "planning.planning_casa_que.gestionar"
pantallas_principales: ["planning.pantalla.planning_casa_que"]
fragmentos: []
acciones: ["filtrar_casas", "elegir_periodo", "ver_planning"]
endpoints: ["/src/planning/planning_casa_que_data"]
estado_revision: "revisado"
---

# Flujo - Planning por casas (filtros)

Entrada del planning por casas: elige grupo de casas y periodo, luego pasa a selección/calendario.

## Objetivo De Usuario

Consultar el calendario de actividades por casas (actual o propuesta de calendario).

## Punto De Entrada

- `planning_casa_que.php` (menú Herramientas de calendario / planning por casas).

## Escenarios

### Preparar filtros

1. Abrir entrada de menú (`propuesta_calendario` opcional).
2. El front llama a `planning_casa_que_data` para acotar `CasasQue` según rol/permiso.
3. Elegir grupo de casas, periodo y si incluir casas sin actividad.
4. Pulsar ver planning → `planning_casa_select.php`.

## Endpoints Del Flujo

- `/src/planning/planning_casa_que_data`

## Errores Conocidos

- `No se encuentra el usuario`

## Ruta de menú

- **Legacy:** `dre > planning > por casas` (y variantes por oficina)
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Planning calendario actual`
- Propuesta: `ACTIVIDADES > Herramientas de calendario > Planning calendario en estudio`
