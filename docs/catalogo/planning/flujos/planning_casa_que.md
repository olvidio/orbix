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

1. Abrir entrada de menú (`propuesta_calendario` opcional; si truthy y year=0 → año+1).
2. El front llama a `planning_casa_que_data` para acotar `CasasQue` según rol/permiso (`PAU_CDC`/`des`/`vcsd`/`mi_sfsv` → `modo_casas`).
3. Elegir grupo de casas (`cdc_sel=9` exige selección manual), periodo y si incluir casas sin actividad.
4. Pulsar ver planning → `planning_casa_select.php`.

## Endpoints Del Flujo

- `/src/planning/planning_casa_que_data`

## Errores Conocidos

- `No se encuentra el usuario`

## Ruta de menú

- **Legacy (actual):** `dre/Calendario/… > planning > por casas` · `adl > Gestión casas > Planing Casas`
- **Pills2 (actual):** `ACTIVIDADES > Herramientas de calendario > Planning calendario actual`
- **Legacy (propuesta):** `adl|Calendario|dre > Nuevo Calendario > nuevo planing`
- **Pills2 (propuesta):** `ACTIVIDADES > Herramientas de calendario > Planning calendario en estudio`
