---
id: "actividadestudios.ca_posibles_que.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Ca Posibles Que"
entidades: ["CaPosiblesQue"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/ca_posibles_que_data"]
pantallas: ["frontend/actividadestudios/controller/ca_posibles_que.php"]
casos_uso: ["src\\actividadestudios\\application\\CaPosiblesQueData"]
tags: ["actividadestudios", "ca", "ca_posibles_que", "data", "posibles", "que"]
estado_revision: "generado"
---

# Gestionar Ca Posibles Que

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ca_posibles_que`.

## Objetivo Funcional

Gestiona CaPosiblesQue. Desplegables y texto de grupo para ca_posibles_que.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadestudios/ca_posibles_que_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/ca_posibles_que.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\CaPosiblesQueData`

## Pistas Desde Endpoints

- Desplegables y texto de grupo para `ca_posibles_que.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
