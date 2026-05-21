---
id: "procesos.procesos_regenerar.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Procesos Regenerar"
entidades: ["ProcesosRegenerar"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_regenerar"]
pantallas: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosRegenerar"]
tags: ["procesos", "procesos_regenerar", "regenerar"]
estado_revision: "generado"
---

# Gestionar Procesos Regenerar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `procesos_regenerar`.

## Objetivo Funcional

Gestiona ProcesosRegenerar. Caso de uso: regenera las tareas del proceso a partir de las fases definidas en tareas_proceso, eliminando las sobrantes.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/procesos_regenerar`

## Pantallas Relacionadas

- `frontend/procesos/controller/procesos_select.php`

## Casos De Uso Detectados

- `src\procesos\application\ProcesosRegenerar`

## Pistas Desde Endpoints

- Caso de uso: regenera las tareas del proceso a partir de las fases definidas en `tareas_proceso`, eliminando las sobrantes.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
