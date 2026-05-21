---
id: "actividades.actividad_select.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Select"
entidades: ["ActividadSelectListado"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_select_datos"]
pantallas: ["frontend/actividades/controller/actividad_select.php"]
casos_uso: ["src\\actividades\\application\\ActividadSelectListado"]
tags: ["actividad", "actividad_select", "actividades", "datos", "select"]
estado_revision: "generado"
---

# Gestionar Actividad Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_select`.

## Objetivo Funcional

Gestiona ActividadSelectListado. JSON del listado para actividad_select: filtros POST → {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_select_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_select.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadSelectListado`

## Pistas Desde Endpoints

- JSON del listado para `actividad_select`: filtros POST → {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
