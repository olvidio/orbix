---
id: "planning.planning_casa_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Elegir las casas y el periodo del planning"
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

Elegir de qué casas se quiere ver el calendario de ocupación y en qué periodo.

## Punto De Entrada

Menú **scdl > planning > por casas** (Pills2: ACTIVIDADES > Herramientas de calendario o CASAS Y CTR).

## Escenarios Inferidos

### Elegir las casas y el periodo del planning

Pasos propuestos:
1. Abrir «BÚSQUEDA DE CASAS CUYO PLANNING INTERESA».
2. Elegir el grupo de casas: **casas sólo sv**, **casas sólo sf**, **casas comunes**, **casas sv**, **casas sf**, **casas y ctr sf**, **una casa o lugar** (y elegirla en el desplegable), **todas las actividades sv** o **todas las actividades sf**.
3. Indicar si se quieren incluir las **casas sin actividad** (no / sí).
4. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
5. Pulsar **Planning vista tabla** (calendario en pantalla) o **vista para imprimir**.

Endpoints asociados:
- `/src/planning/planning_casa_que_data`

## Endpoints Del Flujo

- `/src/planning/planning_casa_que_data`

## Errores Conocidos

- `No se encuentra el usuario`

## Ruta de menú

- **Legacy (actual):** `dre/Calendario/… > planning > por casas` · `adl > Gestión casas > Planing Casas`
- **Pills2 (actual):** `ACTIVIDADES > Herramientas de calendario > Planning calendario actual`
- **Legacy (propuesta):** `adl|Calendario|dre > Nuevo Calendario > nuevo planing`
- **Pills2 (propuesta):** `ACTIVIDADES > Herramientas de calendario > Planning calendario en estudio`
