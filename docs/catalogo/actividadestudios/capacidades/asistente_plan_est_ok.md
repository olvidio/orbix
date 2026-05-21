---
id: "actividadestudios.asistente_plan_est_ok.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Asistente Plan Est Ok"
entidades: ["AsistentePlanEstOk"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/asistente_plan_est_ok"]
pantallas: []
casos_uso: ["src\\actividadestudios\\application\\AsistentePlanEstOk"]
tags: ["actividadestudios", "asistente", "asistente_plan_est_ok", "est", "ok", "plan"]
estado_revision: "generado"
---

# Gestionar Asistente Plan Est Ok

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asistente_plan_est_ok`.

## Objetivo Funcional

Gestiona AsistentePlanEstOk. Marca el flag est_ok (plan de estudios confirmado) de un Asistente. Sustituye al case plan de update_3103.php.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadestudios/asistente_plan_est_ok`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadestudios\application\AsistentePlanEstOk`

## Pistas Desde Endpoints

- Marca el flag `est_ok` (plan de estudios confirmado) de un Asistente. Sustituye al case `plan` de `update_3103.php`.

## Errores Conocidos

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
