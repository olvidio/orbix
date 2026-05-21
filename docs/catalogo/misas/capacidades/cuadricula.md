---
id: "misas.cuadricula.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Cuadricula"
entidades: ["Cuadricula"]
acciones: ["crear_actualizar"]
endpoints: ["/src/misas/cuadricula_update"]
pantallas: ["frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\CuadriculaUpdate"]
tags: ["cuadricula", "misas", "update"]
estado_revision: "generado"
---

# Gestionar Cuadricula

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cuadricula`.

## Objetivo Funcional

Gestiona Cuadricula. Use case del endpoint cuadricula_update (migracion de apps/misas/controller/cuadricula_update.php al Slice 6a). Hace dos cosas en la misma transaccion logica: 1. Upsert / delete de un EncargoDia para un dia + encargo concretos, en funcion de key (si esta vacio, se borra; si trae id_nom, se guarda o actualiza). 2. Recalcula el bloque meta que la UI usa para pintar colores y textos (disponibilidad del sacd anterior y del nuevo, numero de misas del dia, conflictos con primera hora, etc.). El codigo es una traduccion casi literal del controlador original para minimizar riesgo de regresion: la logica de negocio en si no cambia en este slice; lo unico que cambia es donde vive.

## Acciones Detectadas

- `crear_actualizar`

## Endpoints

- `/src/misas/cuadricula_update`

## Pantallas Relacionadas

- `frontend/misas/support/CuadriculaZonaRenderer.php`

## Casos De Uso Detectados

- `src\misas\application\CuadriculaUpdate`

## Pistas Desde Endpoints

- Use case del endpoint `cuadricula_update` (migracion de `apps/misas/controller/cuadricula_update.php` al Slice 6a). Hace dos cosas en la misma transaccion logica: 1. Upsert / delete de un `EncargoDia` para un dia + encargo concretos, en funcion de `key` (si esta vacio, se borra; si trae `id_nom`, se guarda o actualiza). 2. Recalcula el bloque `meta` que la UI usa para pintar colores y textos (disponibilidad del sacd anterior y del nuevo, numero de misas del dia, conflictos con primera hora, etc.). El codigo es una traduccion casi literal del controlador original para minimizar riesgo de regresion: la logica de negocio en si no cambia en este slice; lo unico que cambia es donde vive.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
