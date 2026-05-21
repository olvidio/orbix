---
id: "actividadessacd.sacd.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Sacd"
entidades: ["Sacd"]
acciones: ["eliminar"]
endpoints: ["/src/actividadessacd/sacd_eliminar"]
pantallas: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdEliminar"]
tags: ["actividadessacd", "eliminar", "sacd"]
estado_revision: "generado"
---

# Gestionar Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd`.

## Objetivo Funcional

Gestiona Sacd. Elimina el sacd ({id_activ, id_cargo}) de una actividad y la asistencia asociada.

## Acciones Detectadas

- `eliminar`

## Endpoints

- `/src/actividadessacd/sacd_eliminar`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/activ_sacd.php`

## Casos De Uso Detectados

- `src\actividadessacd\application\SacdEliminar`

## Pistas Desde Endpoints

- Endpoint backend: elimina el sacd ({id_activ, id_cargo}) de una actividad y la asistencia asociada.

## Errores Conocidos

- `no se sabe cual borrar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
