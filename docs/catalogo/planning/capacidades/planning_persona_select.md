---
id: "planning.planning_persona_select.gestionar"
tipo: "capacidad"
modulo: "planning"
nombre: "Gestionar Planning Persona Select"
entidades: ["PlanningPersonaSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_persona_select_data"]
pantallas: ["frontend/planning/controller/planning_persona_select.php"]
casos_uso: ["src\\planning\\application\\PlanningPersonaSelectData"]
tags: ["data", "persona", "planning", "planning_persona_select", "select"]
estado_revision: "generado"
---

# Gestionar Planning Persona Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `planning_persona_select`.

## Objetivo Funcional

Gestiona PlanningPersonaSelect. Listado de personas para planning_persona_select.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/planning/planning_persona_select_data`

## Pantallas Relacionadas

- `frontend/planning/controller/planning_persona_select.php`

## Casos De Uso Detectados

- `src\planning\application\PlanningPersonaSelectData`

## Pistas Desde Endpoints

- Listado de personas para `planning_persona_select`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
