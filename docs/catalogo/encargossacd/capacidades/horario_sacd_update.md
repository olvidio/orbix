---
id: "encargossacd.horario_sacd_update.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Horario Sacd Update"
entidades: ["EncargoSacdHorario"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_sacd_update_data"]
pantallas: ["frontend/encargossacd/controller/horario_sacd_update.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoSacdHorarioUpdate"]
tags: ["data", "encargossacd", "horario", "horario_sacd_update", "sacd", "update"]
estado_revision: "generado"
---

# Gestionar Horario Sacd Update

Propuesta generada automaticamente a partir de endpoints con prefijo comun `horario_sacd_update`.

## Objetivo Funcional

Gestiona EncargoSacdHorario. Alta/edición/baja de horario de encargo sacd (encargo_sacd_horario).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/horario_sacd_update_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/horario_sacd_update.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoSacdHorarioUpdate`

## Pistas Desde Endpoints

- Alta/edición/baja de horario de encargo sacd (`encargo_sacd_horario`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
