---
id: "ubis.direccion.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Direccion"
entidades: ["Direccion"]
acciones: ["crear_actualizar"]
endpoints: ["/src/ubis/direccion_update"]
pantallas: ["frontend/ubis/controller/direccion_update.php"]
casos_uso: ["src\\ubis\\application\\DireccionUpdate"]
tags: ["direccion", "ubis", "update"]
estado_revision: "generado"
---

# Gestionar Direccion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `direccion`.

## Objetivo Funcional

Gestiona Direccion. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear_actualizar`

## Endpoints

- `/src/ubis/direccion_update`

## Pantallas Relacionadas

- `frontend/ubis/controller/direccion_update.php`

## Casos De Uso Detectados

- `src\ubis\application\DireccionUpdate`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

- `no se encuentra el ubi`
- `no se encuentra la dirección`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
