---
id: "planning.planning_casa_que.gestionar"
tipo: "capacidad"
modulo: "planning"
nombre: "Gestionar Planning Casa Que"
entidades: ["PlanningCasaQue"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_casa_que_data"]
pantallas: ["frontend/planning/controller/planning_casa_que.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaQueFormData"]
tags: ["casa", "data", "planning", "planning_casa_que", "que"]
estado_revision: "generado"
---

# Gestionar Planning Casa Que

Propuesta generada automaticamente a partir de endpoints con prefijo comun `planning_casa_que`.

## Objetivo Funcional

Gestiona PlanningCasaQue. Dataset para montar CasasQue en {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/planning/planning_casa_que_data`

## Pantallas Relacionadas

- `frontend/planning/controller/planning_casa_que.php`

## Casos De Uso Detectados

- `src\planning\application\PlanningCasaQueFormData`

## Pistas Desde Endpoints

- Dataset para montar CasasQue en {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
