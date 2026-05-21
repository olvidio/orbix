---
id: "asignaturas.asignaturas_map.gestionar"
tipo: "capacidad"
modulo: "asignaturas"
nombre: "Gestionar Asignaturas Map"
entidades: ["AsignaturasMap"]
acciones: ["obtener_datos"]
endpoints: ["/src/asignaturas/asignaturas_map_data"]
pantallas: []
casos_uso: ["src\\asignaturas\\application\\AsignaturasMapData"]
tags: ["asignaturas", "asignaturas_map", "data", "map"]
estado_revision: "generado"
---

# Gestionar Asignaturas Map

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asignaturas_map`.

## Objetivo Funcional

Gestiona AsignaturasMap. Mapa id_asignatura => nombre_corto para pantallas que no deben usar el contenedor en frontend.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asignaturas/asignaturas_map_data`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\asignaturas\application\AsignaturasMapData`

## Pistas Desde Endpoints

- Mapa id_asignatura => nombre_corto para pantallas que no deben usar el contenedor en frontend.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
