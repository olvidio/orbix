---
id: "encargossacd.horario_update.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Horario Update"
entidades: ["EncargoHorario"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_update_data"]
pantallas: ["frontend/encargossacd/controller/horario_update.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioUpdate"]
tags: ["data", "encargossacd", "horario", "horario_update", "update"]
estado_revision: "generado"
---

# Gestionar Horario Update

Propuesta generada automaticamente a partir de endpoints con prefijo comun `horario_update`.

## Objetivo Funcional

Gestiona EncargoHorario. Alta/edición/baja de horario de encargo (tabla encargo_horario).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/horario_update_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/horario_update.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoHorarioUpdate`

## Pistas Desde Endpoints

- Alta/edición/baja de horario de encargo (tabla encargo_horario).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
