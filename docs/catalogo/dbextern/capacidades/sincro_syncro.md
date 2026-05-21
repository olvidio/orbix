---
id: "dbextern.sincro_syncro.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro Syncro"
entidades: ["SincroPersonas"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_syncro"]
pantallas: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\SincroPersonas"]
tags: ["dbextern", "sincro", "sincro_syncro", "syncro"]
estado_revision: "generado"
---

# Gestionar Sincro Syncro

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro_syncro`.

## Objetivo Funcional

Gestiona SincroPersonas. Sincroniza todas las personas unidas de una DL.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/dbextern/sincro_syncro`

## Pantallas Relacionadas

- `frontend/dbextern/controller/sincro_index.php`

## Casos De Uso Detectados

- `src\dbextern\application\SincroPersonas`

## Pistas Desde Endpoints

- Sincroniza todas las personas unidas de una DL.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
