---
id: "misas.ver_plan_ctr.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Ver Plan Ctr"
entidades: ["VerPlanCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_plan_ctr_data"]
pantallas: ["frontend/misas/controller/imprimir_plan_ctr.php", "frontend/misas/controller/ver_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\VerPlanCtrData"]
tags: ["ctr", "data", "misas", "plan", "ver", "ver_plan_ctr"]
estado_revision: "generado"
---

# Gestionar Ver Plan Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_plan_ctr`.

## Objetivo Funcional

Gestiona VerPlanCtr. Datos para la vista ver_plan_ctr.phtml: cuadricula del plan de misas por centro (filas: encargos, columnas: días).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/ver_plan_ctr_data`

## Pantallas Relacionadas

- `frontend/misas/controller/imprimir_plan_ctr.php`
- `frontend/misas/controller/ver_plan_ctr.php`

## Casos De Uso Detectados

- `src\misas\application\VerPlanCtrData`

## Pistas Desde Endpoints

- Datos para la vista `ver_plan_ctr.phtml`: cuadricula del plan de misas por centro (filas: encargos, columnas: días).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
