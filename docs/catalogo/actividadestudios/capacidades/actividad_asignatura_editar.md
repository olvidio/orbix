---
id: "actividadestudios.actividad_asignatura_editar.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Actividad Asignatura Editar"
entidades: ["ActividadAsignaturaEditar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_editar"]
pantallas: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaEditar"]
tags: ["actividad", "actividad_asignatura_editar", "actividadestudios", "asignatura", "editar"]
estado_revision: "generado"
---

# Gestionar Actividad Asignatura Editar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_asignatura_editar`.

## Objetivo Funcional

Gestiona ActividadAsignaturaEditar. Edita una ActividadAsignatura existente. Sustituye al case editar del antiguo update_3005.php dispatcher.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadestudios/actividad_asignatura_editar`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\ActividadAsignaturaEditar`

## Pistas Desde Endpoints

- Edita una `ActividadAsignatura` existente. Sustituye al case `editar` del antiguo `update_3005.php` dispatcher.

## Errores Conocidos

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha guardado`
- `no encuentro la asignatura`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
