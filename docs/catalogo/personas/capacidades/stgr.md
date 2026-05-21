---
id: "personas.stgr.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Gestionar Stgr"
entidades: ["Stgr"]
acciones: ["crear_actualizar"]
endpoints: ["/src/personas/stgr_update"]
pantallas: ["frontend/personas/view/stgr_cambio.phtml"]
casos_uso: ["src\\personas\\application\\StgrUpdate"]
tags: ["personas", "stgr", "update"]
estado_revision: "generado"
---

# Gestionar Stgr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `stgr`.

## Objetivo Funcional

Gestiona Stgr. Endpoint JSON: actualiza el nivel_stgr de una persona.

## Acciones Detectadas

- `crear_actualizar`

## Endpoints

- `/src/personas/stgr_update`

## Pantallas Relacionadas

- `frontend/personas/view/stgr_cambio.phtml`

## Casos De Uso Detectados

- `src\personas\application\StgrUpdate`

## Pistas Desde Endpoints

- Endpoint JSON: actualiza el `nivel_stgr` de una persona.

## Errores Conocidos

- `No existe la clase de la persona`
- `No se encuentra la persona`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
