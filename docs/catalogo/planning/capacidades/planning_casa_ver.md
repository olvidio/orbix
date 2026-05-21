---
id: "planning.planning_casa_ver.gestionar"
tipo: "capacidad"
modulo: "planning"
nombre: "Gestionar Planning Casa Ver"
entidades: ["PlanningCasaVer"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_casa_ver_data"]
pantallas: ["frontend/planning/controller/planning_casa_ver.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaVerData"]
tags: ["casa", "data", "planning", "planning_casa_ver", "ver"]
estado_revision: "generado"
---

# Gestionar Planning Casa Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `planning_casa_ver`.

## Objetivo Funcional

Gestiona PlanningCasaVer. Dataset para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/planning/planning_casa_ver_data`

## Pantallas Relacionadas

- `frontend/planning/controller/planning_casa_ver.php`

## Casos De Uso Detectados

- `src\planning\application\PlanningCasaVerData`

## Pistas Desde Endpoints

- Dataset para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
