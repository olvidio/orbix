---
id: "misas.ver_plan_sacd.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Ver Plan Sacd"
entidades: ["VerPlanSacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_plan_sacd_data"]
pantallas: ["frontend/misas/controller/ver_plan_sacd.php"]
casos_uso: ["src\\misas\\application\\VerPlanSacdData"]
tags: ["data", "misas", "plan", "sacd", "ver", "ver_plan_sacd"]
estado_revision: "generado"
---

# Gestionar Ver Plan Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_plan_sacd`.

## Objetivo Funcional

Gestiona VerPlanSacd. Datos para la vista ver_plan_sacd.phtml: plan de misas de un sacerdote en un rango de fechas.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/ver_plan_sacd_data`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_plan_sacd.php`

## Casos De Uso Detectados

- `src\misas\application\VerPlanSacdData`

## Pistas Desde Endpoints

- Datos para la vista `ver_plan_sacd.phtml`: plan de misas de un sacerdote en un rango de fechas.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
