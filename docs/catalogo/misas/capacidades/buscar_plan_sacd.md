---
id: "misas.buscar_plan_sacd.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Buscar Plan Sacd"
entidades: ["BuscarPlanSacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/buscar_plan_sacd_data"]
pantallas: ["frontend/misas/controller/buscar_plan_sacd.php"]
casos_uso: ["src\\misas\\application\\BuscarPlanSacdData"]
tags: ["buscar", "buscar_plan_sacd", "data", "misas", "plan", "sacd"]
estado_revision: "generado"
---

# Gestionar Buscar Plan Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `buscar_plan_sacd`.

## Objetivo Funcional

Gestiona BuscarPlanSacd. Lista de sacerdotes disponibles en el buscador del plan SACD (según rol y zona).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/buscar_plan_sacd_data`

## Pantallas Relacionadas

- `frontend/misas/controller/buscar_plan_sacd.php`

## Casos De Uso Detectados

- `src\misas\application\BuscarPlanSacdData`

## Pistas Desde Endpoints

- Lista de sacerdotes disponibles en el buscador del plan SACD (según rol y zona).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
