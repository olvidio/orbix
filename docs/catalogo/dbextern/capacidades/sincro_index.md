---
id: "dbextern.sincro_index.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro Index"
entidades: ["SincroIndex"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/sincro_index_datos"]
pantallas: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\SincroIndexData"]
tags: ["datos", "dbextern", "index", "sincro", "sincro_index"]
estado_revision: "generado"
---

# Gestionar Sincro Index

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro_index`.

## Objetivo Funcional

Gestiona SincroIndex. Calcula los 10 contadores del dashboard de sincronización.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dbextern/sincro_index_datos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/sincro_index.php`

## Casos De Uso Detectados

- `src\dbextern\application\SincroIndexData`

## Pistas Desde Endpoints

- Calcula los 10 contadores del dashboard de sincronización.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
