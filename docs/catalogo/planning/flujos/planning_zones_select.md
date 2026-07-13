---
id: "planning.planning_zones_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Planning por zonas SACD (calendario)"
capacidad: "planning.planning_zones_select.gestionar"
pantallas_principales: ["planning.pantalla.planning_zones_que"]
fragmentos: ["planning.pantalla.planning_zones_select"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_zones_select_data"]
estado_revision: "revisado"
---

# Flujo - Planning por zonas SACD (calendario)

Cuadrícula de actividades por zona SACD tras enviar filtros en `planning_zones_que`.

## Objetivo De Usuario

Visualizar y exportar el planning por zonas en el trimestre elegido.

## Punto De Entrada

- `planning_zones_que.php` → AJAX `planning_zones_select`.

## Escenarios

### Ver calendario

1. Confirmar zona, trimestre, año y actividad.
2. `planning_zones_select_data` devuelve `actividades_por_zona` y cabeceras.
3. Exportar o consultar leyenda.

## Endpoints Del Flujo

- `/src/planning/planning_zones_select_data`

## Ruta de menú

sin entrada de menú en el índice (subflujo del planning por zonas)
