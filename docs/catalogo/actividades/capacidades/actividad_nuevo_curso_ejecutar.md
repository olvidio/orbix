---
id: "actividades.actividad_nuevo_curso_ejecutar.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Nuevo Curso Ejecutar"
entidades: ["ActividadNuevoCursoEjecutar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
pantallas: ["frontend/actividades/controller/actividad_nuevo_curso.php"]
casos_uso: ["src\\actividades\\application\\ActividadNuevoCursoEjecutar"]
tags: ["actividad", "actividad_nuevo_curso_ejecutar", "actividades", "curso", "ejecutar", "nuevo"]
estado_revision: "generado"
---

# Gestionar Actividad Nuevo Curso Ejecutar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_nuevo_curso_ejecutar`.

## Objetivo Funcional

Gestiona ActividadNuevoCursoEjecutar. Endpoint backend para actividad_nuevo_curso (ejecucion).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_nuevo_curso.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadNuevoCursoEjecutar`

## Pistas Desde Endpoints

- Endpoint backend para `actividad_nuevo_curso` (ejecucion).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
