---
id: "misas.modificar_plantilla.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Modificar Plantilla"
entidades: ["PlanDeMisasPantalla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_plantilla_data"]
pantallas: ["frontend/misas/controller/modificar_plantilla.php"]
casos_uso: ["src\\misas\\application\\PlanDeMisasPantallaData"]
tags: ["data", "misas", "modificar", "modificar_plantilla", "plantilla"]
estado_revision: "generado"
---

# Gestionar Modificar Plantilla

Propuesta generada automaticamente a partir de endpoints con prefijo comun `modificar_plantilla`.

## Objetivo Funcional

Gestiona PlanDeMisasPantalla. Datos comunes para las pantallas preparar / modificar / ver plan de misas y para modificar plantilla (mismos desplegables de zona / tipo / orden).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/modificar_plantilla_data`

## Pantallas Relacionadas

- `frontend/misas/controller/modificar_plantilla.php`

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
