---
id: "planning.planning_ctr_select.gestionar"
tipo: "capacidad"
modulo: "planning"
nombre: "Gestionar Planning Ctr Select"
entidades: ["PlanningCtrSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_ctr_select_data"]
pantallas: ["frontend/planning/controller/planning_ctr_select.php"]
casos_uso: ["src\\planning\\application\\PlanningCtrSelectData"]
tags: ["ctr", "data", "planning", "planning_ctr_select", "select"]
estado_revision: "generado"
---

# Gestionar Planning Ctr Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `planning_ctr_select`.

## Objetivo Funcional

Gestiona PlanningCtrSelect. Personas + actividades agrupadas por centro para planning_ctr_select.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/planning/planning_ctr_select_data`

## Pantallas Relacionadas

- `frontend/planning/controller/planning_ctr_select.php`

## Casos De Uso Detectados

- `src\planning\application\PlanningCtrSelectData`

## Pistas Desde Endpoints

- Personas + actividades agrupadas por centro para `planning_ctr_select`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
