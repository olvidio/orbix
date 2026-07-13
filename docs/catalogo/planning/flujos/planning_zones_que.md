---
id: "planning.planning_zones_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Planning por zonas SACD (filtros)"
capacidad: "planning.planning_zones_que.gestionar"
pantallas_principales: ["planning.pantalla.planning_zones_que"]
fragmentos: []
acciones: ["cargar_zonas", "elegir_trimestre", "ver_planning"]
endpoints: ["/src/planning/planning_zones_que_data"]
estado_revision: "revisado"
---

# Flujo - Planning por zonas SACD (filtros)

Entrada del planning por zonas: carga zonas permitidas y elige trimestre/actividad.

## Objetivo De Usuario

Consultar el calendario de actividades SACD agrupadas por zona.

## Punto De Entrada

- `planning_zones_que.php` (menú por zonas o propuesta).

## Escenarios

### Preparar filtros

1. Abrir entrada de menú (`propuesta` opcional).
2. `planning_zones_que_data` devuelve `opciones_zonas` según rol (p-sacd acotado a jefe).
3. Elegir zona, trimestre, año y filtro de actividad.
4. Ver planning → `planning_zones_select`.

## Endpoints Del Flujo

- `/src/planning/planning_zones_que_data`

## Errores Conocidos

- `No se encuentra el usuario`
- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** `dre > planning > por zonas`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > por zonas`
- Propuesta: `dre > propuestas > planing zonas`
