---
id: "actividadestudios.asistente_observ.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Asistente Observ"
entidades: ["AsistenteObserv"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/asistente_observ"]
pantallas: []
casos_uso: ["src\\actividadestudios\\application\\AsistenteObserv"]
tags: ["actividadestudios", "asistente", "asistente_observ", "observ"]
estado_revision: "generado"
---

# Gestionar Asistente Observ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asistente_observ`.

## Objetivo Funcional

Gestiona AsistenteObserv. Guarda el texto observ de un Asistente. Sustituye al case observ de update_3103.php.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadestudios/asistente_observ`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadestudios\application\AsistenteObserv`

## Pistas Desde Endpoints

- Guarda el texto `observ` de un Asistente. Sustituye al case `observ` de `update_3103.php`.

## Errores Conocidos

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
