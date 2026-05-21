---
id: "actividadestudios.actividad_asignatura.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Actividad Asignatura"
entidades: ["ActividadAsignatura"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_eliminar", "/src/actividadestudios/actividad_asignatura_nueva"]
pantallas: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaEliminar", "src\\actividadestudios\\application\\ActividadAsignaturaNueva"]
tags: ["actividad", "actividad_asignatura", "actividadestudios", "asignatura", "eliminar", "nueva"]
estado_revision: "generado"
---

# Gestionar Actividad Asignatura

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_asignatura`.

## Objetivo Funcional

Gestiona ActividadAsignatura. Crea una ActividadAsignatura (asignatura impartida en un ca) y abre el dossier 3005 de la actividad. Sustituye al case nuevo del antiguo update_3005.php dispatcher. Elimina una ActividadAsignatura (asignatura impartida en un ca). Sustituye al case eliminar del antiguo update_3005.php dispatcher.

## Acciones Detectadas

- `crear`
- `eliminar`

## Endpoints

- `/src/actividadestudios/actividad_asignatura_eliminar`
- `/src/actividadestudios/actividad_asignatura_nueva`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\ActividadAsignaturaEliminar`
- `src\actividadestudios\application\ActividadAsignaturaNueva`

## Pistas Desde Endpoints

- Crea una `ActividadAsignatura` (asignatura impartida en un ca) y abre el dossier 3005 de la actividad. Sustituye al case `nuevo` del antiguo `update_3005.php` dispatcher.
- Elimina una `ActividadAsignatura` (asignatura impartida en un ca). Sustituye al case `eliminar` del antiguo `update_3005.php` dispatcher.

## Errores Conocidos

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha borrado`
- `hay un error, no se ha creado`
- `no encuentro la asignatura`
- `sólo se puede eliminar una asignatura desde el dossier de la actividad`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
