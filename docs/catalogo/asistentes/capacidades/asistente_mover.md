---
id: "asistentes.asistente_mover.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Asistente Mover"
entidades: ["AsistenteMover"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/asistente_mover_data"]
pantallas: ["frontend/asistentes/controller/asistente_mover.php"]
casos_uso: ["src\\asistentes\\application\\AsistenteMoverData"]
tags: ["asistente", "asistente_mover", "asistentes", "data", "mover"]
estado_revision: "generado"
---

# Gestionar Asistente Mover

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asistente_mover`.

## Objetivo Funcional

Gestiona AsistenteMover. JSON para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/asistente_mover_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/asistente_mover.php`

## Casos De Uso Detectados

- `src\asistentes\application\AsistenteMoverData`

## Pistas Desde Endpoints

- JSON para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
