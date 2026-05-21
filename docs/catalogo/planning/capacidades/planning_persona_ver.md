---
id: "planning.planning_persona_ver.gestionar"
tipo: "capacidad"
modulo: "planning"
nombre: "Gestionar Planning Persona Ver"
entidades: ["PlanningPersonaVer"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_persona_ver_data"]
pantallas: ["frontend/planning/controller/planning_persona_ver.php"]
casos_uso: ["src\\planning\\application\\PlanningPersonaVerData"]
tags: ["data", "persona", "planning", "planning_persona_ver", "ver"]
estado_revision: "generado"
---

# Gestionar Planning Persona Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `planning_persona_ver`.

## Objetivo Funcional

Gestiona PlanningPersonaVer. Actividades por persona (vista plana) para planning_persona_ver.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/planning/planning_persona_ver_data`

## Pantallas Relacionadas

- `frontend/planning/controller/planning_persona_ver.php`

## Casos De Uso Detectados

- `src\planning\application\PlanningPersonaVerData`

## Pistas Desde Endpoints

- Actividades por persona (vista plana) para `planning_persona_ver`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
