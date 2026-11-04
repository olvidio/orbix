---
id: "planning.planning_zones_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Elegir la zona y el periodo"
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

Elegir la zona de sacerdotes y el trimestre o mes del planning.

## Punto De Entrada

Menú **dre > planning > por zonas** (también **propuestas > planing zonas**).

## Escenarios Inferidos

### Elegir la zona y el periodo

Pasos propuestos:
1. Abrir «trimestre o mes que interesa» y marcar un trimestre (enero-marzo, abril-junio, julio-septiembre, octubre-diciembre) o un mes concreto.
2. Elegir el **año** y la **zona** (según tu rol, solo las tuyas o también «todo»).
3. Marcar si se quieren ver las actividades (sí / no).
4. Pulsar **planning vista tabla**.

Endpoints asociados:
- `/src/planning/planning_zones_que_data`

## Endpoints Del Flujo

- `/src/planning/planning_zones_que_data`

## Errores Conocidos

- `No se encuentra el usuario`
- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** `dre > planning > por zonas` · `vsr > planning > por zonas` · `exterior > sacd > Misas > Planning zonas`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > por zonas`
- Propuesta: `dre > propuestas > planing zonas`
