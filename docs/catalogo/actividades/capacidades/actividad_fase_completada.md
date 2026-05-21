---
id: "actividades.actividad_fase_completada.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Fase Completada"
entidades: ["ActividadFaseCompletadaDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_fase_completada_datos"]
pantallas: []
casos_uso: ["src\\actividades\\application\\ActividadFaseCompletadaDatos"]
tags: ["actividad", "actividad_fase_completada", "actividades", "completada", "datos", "fase"]
estado_revision: "generado"
---

# Gestionar Actividad Fase Completada

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_fase_completada`.

## Objetivo Funcional

Gestiona ActividadFaseCompletadaDatos. JSON: si una fase concreta está completada (paridad con faseCompletada del repositorio).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_fase_completada_datos`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividades\application\ActividadFaseCompletadaDatos`

## Pistas Desde Endpoints

- JSON: si una fase concreta está completada (paridad con faseCompletada del repositorio).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
