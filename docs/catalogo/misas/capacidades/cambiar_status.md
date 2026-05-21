---
id: "misas.cambiar_status.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Cambiar Status"
entidades: ["CambiarStatusPantalla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/cambiar_status_data"]
pantallas: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\CambiarStatusPantallaData"]
tags: ["cambiar", "cambiar_status", "data", "misas", "status"]
estado_revision: "generado"
---

# Gestionar Cambiar Status

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cambiar_status`.

## Objetivo Funcional

Gestiona CambiarStatusPantalla. Formulario "Cambiar estado del plan de misas" (zona, estado, orden).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/cambiar_status_data`

## Pantallas Relacionadas

- `frontend/misas/controller/cambiar_status.php`

## Casos De Uso Detectados

- `src\misas\application\CambiarStatusPantallaData`

## Pistas Desde Endpoints

- Formulario "Cambiar estado del plan de misas" (zona, estado, orden).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
