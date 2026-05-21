---
id: "procesos.actividad_proceso_generar.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Actividad Proceso Generar"
entidades: ["ActividadProcesoGenerar"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/actividad_proceso_generar"]
pantallas: ["frontend/procesos/controller/actividad_proceso.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoGenerar"]
tags: ["actividad", "actividad_proceso_generar", "generar", "proceso", "procesos"]
estado_revision: "generado"
---

# Gestionar Actividad Proceso Generar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_proceso_generar`.

## Objetivo Funcional

Gestiona ActividadProcesoGenerar. Caso de uso: (re)genera las tareas del proceso asociado a un id_activ, conservando el estado actual segun el flag force=true.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/actividad_proceso_generar`

## Pantallas Relacionadas

- `frontend/procesos/controller/actividad_proceso.php`

## Casos De Uso Detectados

- `src\procesos\application\ActividadProcesoGenerar`

## Pistas Desde Endpoints

- Caso de uso: (re)genera las tareas del proceso asociado a un id_activ, conservando el estado actual segun el flag `force=true`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
