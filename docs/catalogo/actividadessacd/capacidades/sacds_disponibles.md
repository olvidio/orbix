---
id: "actividadessacd.sacds_disponibles.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Sacds Disponibles"
entidades: ["SacdsDisponibles"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/sacds_disponibles_data"]
pantallas: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdsDisponiblesData"]
tags: ["actividadessacd", "data", "disponibles", "sacds", "sacds_disponibles"]
estado_revision: "generado"
---

# Gestionar Sacds Disponibles

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacds_disponibles`.

## Objetivo Funcional

Gestiona SacdsDisponibles. Devuelve los sacd candidatos para asignar a una actividad (sacd del centro encargado + sacd globales segun bitmask seleccion).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadessacd/sacds_disponibles_data`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/activ_sacd.php`

## Casos De Uso Detectados

- `src\actividadessacd\application\SacdsDisponiblesData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve los sacd candidatos para asignar a una actividad (sacd del centro encargado + sacd globales segun bitmask `seleccion`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
