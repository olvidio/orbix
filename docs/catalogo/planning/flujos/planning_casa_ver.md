---
id: "planning.planning_casa_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Planning por casas (calendario)"
capacidad: "planning.planning_casa_ver.gestionar"
pantallas_principales: ["planning.pantalla.planning_casa_que", "planning.pantalla.planning_casa_select"]
fragmentos: ["planning.pantalla.planning_casa_ver"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_casa_ver_data"]
estado_revision: "revisado"
---

# Flujo - Planning por casas (calendario)

Muestra la cuadrícula de actividades por casa tras confirmar la selección en `planning_casa_select`.

## Objetivo De Usuario

Visualizar y exportar el planning de casas en el periodo elegido.

## Punto De Entrada

- Desde `planning_casa_select` → AJAX a `planning_casa_ver`.

## Escenarios

### Ver calendario

1. Confirmar casas y periodo en pasos anteriores.
2. `planning_casa_ver` envía `cdc_sel`, fechas ISO, `sin_activ` y lista manual si aplica.
3. Renderiza actividades y periodos de ocupación por ubi.
4. Opcional: exportar o abrir leyenda.

## Endpoints Del Flujo

- `/src/planning/planning_casa_ver_data`

## Errores Conocidos

- `Faltan fechas de periodo (f_ini_iso / f_fin_iso).`

## Ruta de menú

sin entrada de menú en el índice (subflujo del planning por casas)
