---
id: "misas.nuevo_status.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Nuevo Status"
entidades: ["NuevoStatusPeriodo"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/nuevo_status"]
pantallas: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\NuevoStatusPeriodo"]
tags: ["misas", "nuevo", "nuevo_status", "status"]
estado_revision: "generado"
---

# Gestionar Nuevo Status

Propuesta generada automaticamente a partir de endpoints con prefijo comun `nuevo_status`.

## Objetivo Funcional

Gestiona NuevoStatusPeriodo. Actualiza status de todos los EncargoDia de encargos 8100+ de la zona en el rango.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/nuevo_status`

## Pantallas Relacionadas

- `frontend/misas/controller/cambiar_status.php`

## Casos De Uso Detectados

- `src\misas\application\NuevoStatusPeriodo`

## Pistas Desde Endpoints

- Actualiza `status` de todos los `EncargoDia` de encargos 8100+ de la zona en el rango.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
