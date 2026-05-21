---
id: "misas.buscar_plan_ctr.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Buscar Plan Ctr"
entidades: ["BuscarPlanCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/buscar_plan_ctr_data"]
pantallas: ["frontend/misas/controller/buscar_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\BuscarPlanCtrData"]
tags: ["buscar", "buscar_plan_ctr", "ctr", "data", "misas", "plan"]
estado_revision: "generado"
---

# Gestionar Buscar Plan Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `buscar_plan_ctr`.

## Objetivo Funcional

Gestiona BuscarPlanCtr. Formulario buscador del plan de misas por centro (zonas + centros + periodo).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/buscar_plan_ctr_data`

## Pantallas Relacionadas

- `frontend/misas/controller/buscar_plan_ctr.php`

## Casos De Uso Detectados

- `src\misas\application\BuscarPlanCtrData`

## Pistas Desde Endpoints

- Formulario buscador del plan de misas por centro (zonas + centros + periodo).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
