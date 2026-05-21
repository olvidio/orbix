---
id: "actividadestudios.matricula_automatica.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Matricula Automatica"
entidades: ["MatriculaAutomatica"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/matricula_automatica"]
pantallas: ["frontend/actividadestudios/controller/matricular.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaAutomatica"]
tags: ["actividadestudios", "automatica", "matricula", "matricula_automatica"]
estado_revision: "generado"
---

# Gestionar Matricula Automatica

Propuesta generada automaticamente a partir de endpoints con prefijo comun `matricula_automatica`.

## Objetivo Funcional

Gestiona MatriculaAutomatica. Matricula masivamente a una o varias personas en las asignaturas del plan de estudios de su actividad vigente.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadestudios/matricula_automatica`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/matricular.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\MatriculaAutomatica`

## Pistas Desde Endpoints

- Matricula masivamente a una o varias personas en las asignaturas del plan de estudios de su actividad vigente.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
