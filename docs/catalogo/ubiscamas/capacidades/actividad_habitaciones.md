---
id: "ubiscamas.actividad_habitaciones.gestionar"
tipo: "capacidad"
modulo: "ubiscamas"
nombre: "Gestionar Actividad Habitaciones"
entidades: ["HabitacionesCamaLista"]
acciones: ["listar"]
endpoints: ["/src/ubiscamas/actividad_habitaciones_lista"]
pantallas: ["frontend/ubiscamas/controller/lista_habitaciones.php", "frontend/ubiscamas/controller/lista_habitaciones_distribucion.php", "frontend/ubiscamas/controller/lista_habitaciones_nombres.php"]
casos_uso: ["src\\ubiscamas\\application\\HabitacionesCamaLista"]
tags: ["actividad", "actividad_habitaciones", "habitaciones", "lista", "ubiscamas"]
estado_revision: "generado"
---

# Gestionar Actividad Habitaciones

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_habitaciones`.

## Objetivo Funcional

Gestiona HabitacionesCamaLista. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/ubiscamas/actividad_habitaciones_lista`

## Pantallas Relacionadas

- `frontend/ubiscamas/controller/lista_habitaciones.php`
- `frontend/ubiscamas/controller/lista_habitaciones_distribucion.php`
- `frontend/ubiscamas/controller/lista_habitaciones_nombres.php`

## Casos De Uso Detectados

- `src\ubiscamas\application\HabitacionesCamaLista`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
