---
id: "planning.planning_zones_que.gestionar"
tipo: "capacidad"
modulo: "planning"
nombre: "Gestionar Planning Zones Que"
entidades: ["PlanningZonesQue"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_zones_que_data"]
pantallas: ["frontend/planning/controller/planning_zones_que.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesQueData"]
tags: ["data", "planning", "planning_zones_que", "que", "zones"]
estado_revision: "generado"
---

# Gestionar Planning Zones Que

Propuesta generada automaticamente a partir de endpoints con prefijo comun `planning_zones_que`.

## Objetivo Funcional

Gestiona PlanningZonesQue. Opciones de zona + comprobación de permiso para planning_zones_que.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/planning/planning_zones_que_data`

## Pantallas Relacionadas

- `frontend/planning/controller/planning_zones_que.php`

## Casos De Uso Detectados

- `src\planning\application\PlanningZonesQueData`

## Pistas Desde Endpoints

- Opciones de zona + comprobación de permiso para `planning_zones_que`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
