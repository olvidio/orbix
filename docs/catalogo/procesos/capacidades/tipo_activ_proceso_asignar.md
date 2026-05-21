---
id: "procesos.tipo_activ_proceso_asignar.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Tipo Activ Proceso Asignar"
entidades: ["TipoActivProcesoAsignar"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/tipo_activ_proceso_asignar"]
pantallas: ["frontend/procesos/controller/tipo_activ_proceso.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoAsignar"]
tags: ["activ", "asignar", "proceso", "procesos", "tipo", "tipo_activ_proceso_asignar"]
estado_revision: "generado"
---

# Gestionar Tipo Activ Proceso Asignar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ_proceso_asignar`.

## Objetivo Funcional

Gestiona TipoActivProcesoAsignar. Caso de uso: asigna un id_tipo_proceso al tipo de actividad indicado, distinguiendo entre proceso propio (dl) o no-propio segun propio.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/tipo_activ_proceso_asignar`

## Pantallas Relacionadas

- `frontend/procesos/controller/tipo_activ_proceso.php`

## Casos De Uso Detectados

- `src\procesos\application\TipoActivProcesoAsignar`

## Pistas Desde Endpoints

- Caso de uso: asigna un id_tipo_proceso al tipo de actividad indicado, distinguiendo entre proceso propio (dl) o no-propio segun `propio`.

## Errores Conocidos

- `hay un error, no se ha guardado el proceso`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
