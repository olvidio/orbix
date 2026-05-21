---
id: "actividadescentro.centros_disponibles.gestionar"
tipo: "capacidad"
modulo: "actividadescentro"
nombre: "Gestionar Centros Disponibles"
entidades: ["CentrosDisponibles"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/centros_disponibles_data"]
pantallas: []
casos_uso: ["src\\actividadescentro\\application\\CentrosDisponiblesData"]
tags: ["actividadescentro", "centros", "centros_disponibles", "data", "disponibles"]
estado_revision: "generado"
---

# Gestionar Centros Disponibles

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centros_disponibles`.

## Objetivo Funcional

Gestiona CentrosDisponibles. Devuelve los centros disponibles (candidatos) para asignar como encargado de una actividad, filtrados por tipo (sg / sr / nagd / sssc / sfsg / sfsr / sfnagd).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadescentro/centros_disponibles_data`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadescentro\application\CentrosDisponiblesData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve los centros disponibles (candidatos) para asignar como encargado de una actividad, filtrados por `tipo` (sg / sr / nagd / sssc / sfsg / sfsr / sfnagd).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
