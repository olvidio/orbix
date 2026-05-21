---
id: "procesos.procesos_clonar.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Procesos Clonar"
entidades: ["ProcesosClonar"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_clonar"]
pantallas: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosClonar"]
tags: ["clonar", "procesos", "procesos_clonar"]
estado_revision: "generado"
---

# Gestionar Procesos Clonar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `procesos_clonar`.

## Objetivo Funcional

Gestiona ProcesosClonar. Caso de uso: clona las tareas de un proceso de referencia al proceso indicado (borrando las existentes previamente). Devuelve '' si ha ido bien o un mensaje de error. El frontend se encarga de recargar la vista del proceso tras el clonado.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/procesos_clonar`

## Pantallas Relacionadas

- `frontend/procesos/controller/procesos_select.php`

## Casos De Uso Detectados

- `src\procesos\application\ProcesosClonar`

## Pistas Desde Endpoints

- Caso de uso: clona las tareas de un proceso de referencia al proceso indicado (borrando las existentes previamente). Devuelve '' si ha ido bien o un mensaje de error. El frontend se encarga de recargar la vista del proceso tras el clonado.

## Errores Conocidos

- `no se ha indicado el proceso a clonar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
