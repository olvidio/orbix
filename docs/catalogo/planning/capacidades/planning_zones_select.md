---
id: "planning.planning_zones_select.gestionar"
tipo: "capacidad"
modulo: "planning"
nombre: "Gestionar Planning Zones Select"
entidades: ["PlanningZonesSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_zones_select_data"]
pantallas: ["frontend/planning/controller/planning_zones_select.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesSelectData"]
tags: ["data", "planning", "planning_zones_select", "select", "zones"]
estado_revision: "generado"
---

# Gestionar Planning Zones Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `planning_zones_select`.

## Objetivo Funcional

Gestiona PlanningZonesSelect. Dataset para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/planning/planning_zones_select_data`

## Pantallas Relacionadas

- `frontend/planning/controller/planning_zones_select.php`

## Casos De Uso Detectados

- `src\planning\application\PlanningZonesSelectData`

## Pistas Desde Endpoints

- Dataset para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
