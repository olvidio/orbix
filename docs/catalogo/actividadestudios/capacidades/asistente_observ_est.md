---
id: "actividadestudios.asistente_observ_est.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Asistente Observ Est"
entidades: ["AsistenteObservEst"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/asistente_observ_est"]
pantallas: []
casos_uso: ["src\\actividadestudios\\application\\AsistenteObservEst"]
tags: ["actividadestudios", "asistente", "asistente_observ_est", "est", "observ"]
estado_revision: "generado"
---

# Gestionar Asistente Observ Est

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asistente_observ_est`.

## Objetivo Funcional

Gestiona AsistenteObservEst. Guarda el texto observ_est de un Asistente (persona en una actividad de estudios). Sustituye al case observ_est de update_3103.php.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadestudios/asistente_observ_est`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadestudios\application\AsistenteObservEst`

## Pistas Desde Endpoints

- Guarda el texto `observ_est` de un Asistente (persona en una actividad de estudios). Sustituye al case `observ_est` de `update_3103.php`.

## Errores Conocidos

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
