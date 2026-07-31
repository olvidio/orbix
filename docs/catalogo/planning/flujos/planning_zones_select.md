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

Visualizar y exportar el planning por zonas en el trimestre/mes elegido (calendario actual o propuesta).

## Punto De Entrada

- `planning_zones_que.php` → AJAX `planning_zones_select`.

## Escenarios

### Ver calendario actual

1. Confirmar zona (`todo`/`todo_propias` si jefe), trimestre/mes, año y `actividad=si|no`.
2. Sin `propuesta`: el servicio filtra actividades en status ACTUAL.
3. `planning_zones_select_data` devuelve `actividades_por_zona` y cabeceras.
4. Exportar o consultar leyenda.

### Ver propuesta de calendario

1. Entrada de menú con `propuesta=true` (o hidden propagado).
2. Status ≠ borrable; UI muestra banner de propuesta.
3. Resto igual que el calendario actual.

### Cuadrícula limpia

1. Elegir `actividad=no` → slots vacíos por persona (útil para imprimir plantilla).

## Endpoints Del Flujo

- `/src/planning/planning_zones_select_data`

## Ruta de menú

sin entrada de menú en el índice (subflujo del planning por zonas)
