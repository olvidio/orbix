---
id: "misas.plan_de_misas_pantalla.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Plan De Misas Pantalla"
entidades: ["PlanDeMisasPantalla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
pantallas: ["frontend/misas/controller/modificar_plan_de_misas.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_plan_de_misas.php"]
casos_uso: ["src\\misas\\application\\PlanDeMisasPantallaData"]
tags: ["data", "de", "misas", "pantalla", "plan", "plan_de_misas_pantalla"]
estado_revision: "generado"
---

# Gestionar Plan De Misas Pantalla

Propuesta generada automaticamente a partir de endpoints con prefijo comun `plan_de_misas_pantalla`.

## Objetivo Funcional

Gestiona PlanDeMisasPantalla. Datos comunes para las pantallas preparar / modificar / ver plan de misas y para modificar plantilla (mismos desplegables de zona / tipo / orden).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/plan_de_misas_pantalla_data`

## Pantallas Relacionadas

- `frontend/misas/controller/modificar_plan_de_misas.php`
- `frontend/misas/controller/preparar_plan_de_misas.php`
- `frontend/misas/controller/ver_plan_de_misas.php`

## Casos De Uso Detectados

- `src\misas\application\PlanDeMisasPantallaData`

## Pistas Desde Endpoints

- Datos comunes para las pantallas preparar / modificar / ver plan de misas y para modificar plantilla (mismos desplegables de zona / tipo / orden).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
