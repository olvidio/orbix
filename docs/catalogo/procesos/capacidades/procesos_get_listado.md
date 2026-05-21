---
id: "procesos.procesos_get_listado.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Procesos Get Listado"
entidades: ["ProcesosGetListado"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_get_listado"]
pantallas: ["frontend/procesos/controller/procesos_get_listado.php"]
casos_uso: ["src\\procesos\\application\\ProcesosGetListado"]
tags: ["get", "listado", "procesos", "procesos_get_listado"]
estado_revision: "generado"
---

# Gestionar Procesos Get Listado

Propuesta generada automaticamente a partir de endpoints con prefijo comun `procesos_get_listado`.

## Objetivo Funcional

Gestiona ProcesosGetListado. Caso de uso: devuelve el listado (estructurado) de fases/tareas del proceso filtrando por sfsv/role. El render HTML se hace en el frontend.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/procesos_get_listado`

## Pantallas Relacionadas

- `frontend/procesos/controller/procesos_get_listado.php`

## Casos De Uso Detectados

- `src\procesos\application\ProcesosGetListado`

## Pistas Desde Endpoints

- Caso de uso: devuelve el listado (estructurado) de fases/tareas del proceso filtrando por sfsv/role. El render HTML se hace en el frontend.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
